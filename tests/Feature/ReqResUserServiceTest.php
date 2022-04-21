<?php

namespace Tests\Feature;

use App\Models\User;
use App\Service\ReqRes\ReqResUserService;
use App\Service\ReqRes\ReqResUserServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReqResUserServiceTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    public function test_implementation()
    {
        $this->assertInstanceOf(ReqResUserService::class, $this->getService());
    }

    public function test_create_new_user()
    {
        $reqResUser = [
            'id' => 1,
            'first_name' => $this->faker()->firstName(),
            'last_name' => $this->faker()->lastName(),
            'email' => $this->faker()->safeEmail,
            'avatar' => $this->faker()->imageUrl(),
        ];
        $service = $this->getService();
        $user = $service->createOrUpdateUser($reqResUser);
        $this->assertDatabaseHas('users', [
            'first_name' => $reqResUser['first_name'],
            'last_name' => $reqResUser['last_name'],
            'email' => $reqResUser['email'],
            'avatar' => $reqResUser['avatar'],
            'external_id' => $reqResUser['id'],
        ]);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($reqResUser['email'], $user->email);
    }

    public function test_update_existing_user()
    {
        $userData = [
            'first_name' => 'Bob',
            'last_name' => $this->faker()->lastName(),
            'email' => $this->faker()->safeEmail,
            'avatar' => $this->faker()->imageUrl(),
        ];
        $externalId = 1;
        // Store an existing user
        $user = new User(array_merge($userData, [
            'external_id' => $externalId,
        ]));
        $user->save();

        // User changed first name on ReqRes API
        $reqResUser = array_merge($userData, [
            'id' => $externalId,
            'first_name' => 'John',
        ]);

        $service = $this->getService();
        $user = $service->createOrUpdateUser($reqResUser);
        $this->assertDatabaseHas('users', [
            'first_name' => $reqResUser['first_name'],
            'last_name' => $reqResUser['last_name'],
            'email' => $reqResUser['email'],
            'avatar' => $reqResUser['avatar'],
            'external_id' => $externalId,
        ]);
        $this->assertEquals('John', $user->first_name);
    }

    private function getService(): ReqResUserServiceInterface
    {
        return app(ReqResUserServiceInterface::class);
    }
}
