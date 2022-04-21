<?php

namespace App\Service\ReqRes;

use App\Models\User;

interface ReqResUserServiceInterface
{
    public function createOrUpdateUser(array $reqResUser): User;
}