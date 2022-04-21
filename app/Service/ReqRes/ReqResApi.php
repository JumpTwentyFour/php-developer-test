<?php

namespace App\Service\ReqRes;

use App\Exceptions\ReqResApiException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ReqResApi implements ReqResApiInterface
{
    public const API_URL = 'https://reqres.in';

    public function getUsers(int $page = 1): Response
    {
        $response = Http::get(static::API_URL.'/api/users', [
            'page' => $page,
        ]);
        if ($response->successful()) {
            return $response;
        }

        throw new ReqResApiException("ReqReq API request failed with status of {$response->status()}");
    }

    public function getAllUsers(): array
    {
        $page = 1;
        $response = $this->getUsers($page);
        $totalPages = $response['total_pages'];
        $users = $response['data'];
        $page++;
        while ($page <= $totalPages) {
            $users = array_merge($users, $this->getUsers($page)['data']);
            $page++;
        }

        return $users;
    }

}