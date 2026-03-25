<?php

declare(strict_types=1);

namespace App\Commands;

use App\Services\ApiClient;
use App\Services\ConfigStore;
use App\Support\DatabaseConnector;
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
            $this->info('Token configured. Run `illuminate` to begin.');

            return self::SUCCESS;
        }

        $token = $config->getToken();
        if (! $token) {
            $this->error('No token configured. Run: illuminate --token=<your-token>');

            return self::FAILURE;
        }

        if ($flag = $this->option('flag')) {
            return $this->submitFlag($token, $flag);
        }

        return $this->showStage($token);
    }

    private function showStage(string $token): int
    {
        // This intentionally breaks — the candidate must read the source
        $connector = new DatabaseConnector();

        return $connector->initialize($this);
    }

    private function submitFlag(string $token, string $flag): int
    {
        $client = new ApiClient($token);
        $result = $client->submitAnswer($flag);

        if (($result['status'] ?? '') === 'correct') {
            $this->info('Correct.');

            return self::SUCCESS;
        }

        $this->error('Incorrect.');

        return self::FAILURE;
    }
}
