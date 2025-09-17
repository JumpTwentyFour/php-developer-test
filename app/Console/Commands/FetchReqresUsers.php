<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\ReqresApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchReqresUsers extends Command
{
    protected $signature = 'reqres:fetch-users {--page=1 : The page number to fetch} {--fetch-all : Fetch all available pages}';

    protected $description = 'Fetch users from Reqres API and store them in the database';

    private ReqresApiService $reqresService;

    public function __construct(ReqresApiService $reqresService)
    {
        parent::__construct();
        $this->reqresService = $reqresService;
    }

    private function processUsersFromResponse(array $response): void
    {
        foreach ($response['data'] as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['first_name'] . ' ' . $userData['last_name'],
                    'reqres_id' => $userData['id'],
                    'password' => bcrypt('password'),
                ]
            );
        }
    }

    public function handle()
    {
        try {
            if ($this->option('fetch-all')) {
                $response = $this->reqresService->getUsers(1);
                $totalPages = $response['total_pages'] ?? 1;

                $this->info("Fetching all pages (total: {$totalPages})");

                $this->info('Processing page 1 of users');
                $this->processUsersFromResponse($response);

                for ($page = 2; $page <= $totalPages; $page++) {
                    $response = $this->reqresService->getUsers($page);
                    $this->info("Processing page {$page} of users");
                    $this->processUsersFromResponse($response);
                }

                $this->info('Completed fetching all pages');
            } else {
                $page = (int) $this->option('page');
                $response = $this->reqresService->getUsers($page);
                $this->info("Processing page {$page} of users");
                $this->processUsersFromResponse($response);
                $this->info("Completed fetching page {$page} of users");
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch users from Reqres API', [
                'error' => $e->getMessage(),
                'fetch_all' => $this->option('fetch-all'),
                'page' => $this->option('page'),
            ]);
            $this->error($e->getMessage());

            return 1;
        }

        return 0;
    }
}
