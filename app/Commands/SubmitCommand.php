<?php

declare(strict_types=1);

namespace App\Commands;

use App\Services\ApiClient;
use App\Services\ConfigStore;
use LaravelZero\Framework\Commands\Command;

class SubmitCommand extends Command
{
    protected $signature = 'submit {answer : Your answer for the current stage}';

    protected $description = 'Submit an answer for the current challenge stage';

    public function handle(ConfigStore $config): int
    {
        $token = $config->getToken();
        if (! $token) {
            $this->components->error('No token configured. Run: illuminate --token=<your-token>');

            return self::FAILURE;
        }

        $client = new ApiClient($token);
        $result = $client->submitAnswer($this->argument('answer'));

        if (($result['status'] ?? '') === 'correct') {
            $this->components->info('Correct.');

            return self::SUCCESS;
        }

        $this->components->error('Incorrect.');

        return self::FAILURE;
    }
}
