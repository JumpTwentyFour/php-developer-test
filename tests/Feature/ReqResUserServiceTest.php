<?php

namespace Tests\Feature;

use App\Service\ReqRes\ReqResUserService;
use App\Service\ReqRes\ReqResUserServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReqResUserServiceTest extends TestCase
{
    public function test_implementation()
    {
        $this->assertInstanceOf(ReqResUserService::class, $this->getService());
    }

    private function getService(): ReqResUserServiceInterface
    {
        return app(ReqResUserServiceInterface::class);
    }
}
