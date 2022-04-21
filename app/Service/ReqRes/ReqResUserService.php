<?php

namespace App\Service\ReqRes;

use App\Models\User;

class ReqResUserService implements ReqResUserServiceInterface
{
    public function createOrUpdateUser(array $reqResUser): User
    {
        $data = $reqResUser;
        $externalId = $data['id'];
        unset($data['id']);
        $user = User::updateOrCreate([
            'external_id' => $externalId,
        ], $data);

        return $user;
    }
}