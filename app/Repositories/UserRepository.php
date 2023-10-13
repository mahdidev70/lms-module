<?php

namespace App\Repositories;

use App\Models\UserProfile;
use App\Repositories\Interfaces\UserRepositoryInterface;


class UserRepository implements UserRepositoryInterface
{
    public function getById($id)
    {
        return UserProfile::where('id', $id)->firstOrFail();
    }
}
