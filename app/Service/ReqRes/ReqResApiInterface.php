<?php

namespace App\Service\ReqRes;

use Illuminate\Http\Client\Response;

interface ReqResApiInterface {
    public function getUsers(int $page = 1): Response;

    public function getAllUsers(): array;
}