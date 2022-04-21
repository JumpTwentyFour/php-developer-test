<?php

namespace App\Console\Commands;

use App\Exceptions\ReqResApiException;
use App\Service\ReqRes\ReqResApiInterface;
use App\Service\ReqRes\ReqResUserService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes users with the ReqRes API';

    private $apiService;
    private $userService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ReqResApiInterface $apiService, ReqResUserService $userService)
    {
        parent::__construct();
        $this->apiService = $apiService;
        $this->userService = $userService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $users = $this->apiService->getAllUsers();
        } catch (ReqResApiException $e) {
            Log::error($e->getMessage());
            $this->error("Request to ReqRes API failed, check logs and try again");

            return self::FAILURE;
        }
        $count = count($users);
        $bar = $this->output->createProgressBar($count);
        foreach ($users as $user) {
            $this->userService->createOrUpdateUser($user);
            $bar->advance();
        }
        $bar->finish();

        return self::SUCCESS;
    }
}
