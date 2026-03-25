<?php

declare(strict_types=1);

namespace App\Commands;

use App\Services\ApiClient;
use App\Services\ConfigStore;
use App\Support\DatabaseConnector;
use Exception;
use LaravelZero\Framework\Commands\Command;

class IlluminateCommand extends Command
{
    protected $signature = 'illuminate
        {--token= : Your authentication token}
        {--flag= : Submit a discovered flag}';

    protected $description = 'Illuminate — Bi-Tech Hiring Challenge';

    public function handle(ConfigStore $config): int
    {
        if ($token = $this->option('token')) {
            $config->setToken($token);
            $this->components->info('Token configured. Run `illuminate` to begin.');

            return self::SUCCESS;
        }

        $token = $config->getToken();
        if (! $token) {
            $this->components->error('No token configured. Run: illuminate --token=<your-token>');

            return self::FAILURE;
        }

        if ($flag = $this->option('flag')) {
            return $this->submitFlag($token, $flag);
        }

        return $this->showStage($token);
    }

    private function showStage(string $token): int
    {
        $client = new ApiClient($token);

        try {
            $challenge = $client->getChallenge();
        } catch (Exception) {
            $connector = new DatabaseConnector;
            if (! $connector->verify()) {
                $this->components->error('Something Went Wrong: Database connection failed — ensure your .env is configured correctly.');

                return self::FAILURE;
            }

            return self::SUCCESS;
        }

        // If still at registered or stage_0, show the break
        if (in_array($challenge['stage'] ?? '', ['registered', 'stage_0'], true)) {
            $connector = new DatabaseConnector;
            if (! $connector->verify()) {
                $this->components->error('Something Went Wrong: Database connection failed — ensure your .env is configured correctly.');

                return self::FAILURE;
            }

            return self::SUCCESS;
        }

        // Show current stage instructions
        $stage = is_string($challenge['stage'] ?? null) ? $challenge['stage'] : 'unknown';
        $this->components->info("Stage: {$stage}");
        $this->newLine();
        $instructions = $challenge['instructions'] ?? 'No instructions available.';
        $this->line(is_string($instructions) ? $instructions : 'No instructions available.');
        $this->newLine();

        // Download SSH key for Stage 2+
        if (in_array($challenge['stage'] ?? '', ['stage_2', 'stage_3'], true)) {
            $this->downloadSshKey($client);
        }

        return self::SUCCESS;
    }

    private function downloadSshKey(ApiClient $client): void
    {
        $configStore = app(ConfigStore::class);
        $keyPath = $configStore->configDir().'/key';

        if (file_exists($keyPath)) {
            return;
        }

        try {
            $this->components->task('Downloading SSH key', function () use ($client, $keyPath): bool {
                $sshData = $client->getSshKey();
                $privateKey = $sshData['private_key'] ?? '';
                if ($privateKey === '') {
                    return false;
                }
                file_put_contents($keyPath, $privateKey);
                chmod($keyPath, 0600);

                return true;
            });
        } catch (Exception) {
            // Silently fail — they can retry
        }
    }

    private function submitFlag(string $token, string $flag): int
    {
        $client = new ApiClient($token);
        $result = $client->submitAnswer($flag);

        if (($result['status'] ?? '') === 'correct') {
            $this->components->info('Correct.');

            return self::SUCCESS;
        }

        $this->components->error('Incorrect.');

        return self::FAILURE;
    }
}
