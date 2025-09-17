<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class ReqresApiService
{
    private string $baseUrl = 'https://reqres.in/api';

    private array $headers;

    public function __construct()
    {
        $this->headers = [
            'x-api-key' => env('REQRES_API_KEY'),
            'Accept' => 'application/json',
        ];
    }

    public function getUsers(int $page = 1): array
    {
        $response = Http::withHeaders($this->headers)
            ->get("{$this->baseUrl}/users", [
                'page' => $page,
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Failed to fetch users from Reqres API: ' . $response->body());
        }

        return $response->json();
    }
}
