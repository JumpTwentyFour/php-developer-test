<?php

namespace Tests\Feature;

use App\Exceptions\ReqResApiException;
use App\Service\ReqRes\ReqResApi;
use App\Service\ReqRes\ReqResApiInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Tests that interact with the ReqRes API.
 * 
 * These tests should not be run in CI due to their dependancy on the live API
 */
class ReqResApiTest extends TestCase
{
    /**
     * @group reqres_api
     */
    public function test_implementation()
    {
        $service = $this->getApiService();

        $this->assertInstanceOf(ReqResApi::class, $service);
    }

    /**
     * @group reqres_api
     */
    public function test_get_users_return_type()
    {
        $service = $this->getApiService();

        $response = $service->getUsers();

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @group reqres_api
     */
    public function test_get_users()
    {
        $service = $this->getApiService();

        $response = $service->getUsers();

        $body = $response->json();
        $this->assertNotEmpty($body['data']);
        $user = $body['data'][0];

        $this->assertArrayHasKey('id', $user);
        $this->assertArrayHasKey('email', $user);
        $this->assertArrayHasKey('first_name', $user);
        $this->assertArrayHasKey('last_name', $user);
        $this->assertArrayHasKey('avatar', $user);
    }

    /**
     * @group reqres_api
     */
    public function test_get_users_by_page()
    {
        $service = $this->getApiService();

        // Test default page number
        $response = $service->getUsers();

        $body = $response->json();

        $this->assertEquals(1, $body['page']);
        $this->assertNotEmpty($body['data']);

        // Test next valid page number
        $response = $service->getUsers(2);

        $body = $response->json();

        $this->assertEquals(2, $body['page']);
        $this->assertNotEmpty($body['data']);

        // Given an invalid page number
        $response = $service->getUsers(100000);

        $body = $response->json();

        $this->assertEmpty($body['data']);
    }

    /**
     * @group reqres_api
     */
    public function test_get_all_users()
    {
        $service = $this->getApiService();

        $initialResponse = $service->getUsers();
        $total = $initialResponse['total'];
        $users = $service->getAllUsers();

        // Ensure our collection looks correct
        $this->assertIsArray($users);
        $this->assertNotEmpty($users);
        $this->assertCount($total, $users);

        // Check the structure of the first user
        $user = $users[0];
        $this->assertNotNull($user['id']);
        $this->assertNotNull($user['first_name']);
        $this->assertNotNull($user['last_name']);
        $this->assertNotNull($user['email']);
        $this->assertNotNull($user['avatar']);

        // Assert all users in the collection are unique
        $uniqueIds = array_unique(array_map(function ($user) {
            return $user['id'];
        }, $users));
        $this->assertCount($total, $uniqueIds);
    }

    public function test_server_error()
    {
        $service = $this->getApiService();
        Http::fake([ReqResApi::API_URL.'/api/*' => Http::response('', 500)]);

        $this->expectException(ReqResApiException::class);
        $service->getUsers();
    }

    private function getApiService(): ReqResApiInterface
    {
        return app(ReqResApiInterface::class);
    }
}
