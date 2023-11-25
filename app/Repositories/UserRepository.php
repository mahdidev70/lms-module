<?php

namespace TechStudio\Lms\app\Repositories;

use TechStudio\Core\app\Models\UserProfile;
use TechStudio\Lms\app\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function getById($id)
    {
        return UserProfile::where('id', $id)->firstOrFail();
    }
}
