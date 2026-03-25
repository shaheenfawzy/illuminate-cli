<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ApiClient
{
    private string $baseUrl;

    public function __construct(
        private readonly string $token,
    ) {
        $this->baseUrl = 'https://illuminate.bitech.dev/api';
    }

    /** @return array<string, mixed> */
    public function getChallenge(): array
    {
        $response = Http::withToken($this->token)
            ->get("{$this->baseUrl}/challenge");

        $response->throw();

        return $response->json();
    }

    /** @return array<string, mixed> */
    public function submitAnswer(string $answer): array
    {
        $response = Http::withToken($this->token)
            ->post("{$this->baseUrl}/challenge/submit", [
                'answer' => $answer,
            ]);

        $response->throw();

        return $response->json();
    }

    /** @return array<string, mixed> */
    public function getSshKey(): array
    {
        $response = Http::withToken($this->token)
            ->get("{$this->baseUrl}/challenge/ssh-key");

        $response->throw();

        return $response->json();
    }
}
