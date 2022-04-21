<?php

namespace App\Service\ReqRes;

use App\Models\User;

class ReqResUserService implements ReqResUserServiceInterface
{
    public function createOrUpdateUser(array $reqResUser): User
    {
        return new User();
    }
}