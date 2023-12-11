<?php

namespace TechStudio\Lms\app\Repositories;

use TechStudio\Core\app\Models\UserProfile;
use TechStudio\Lms\app\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function getById($user)
    {
        $userProfile = UserProfile::where('user_id', $user->id)->first();
        if (!$userProfile){
            $userProfile = UserProfile::create([
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'registration_phone_number' => $user->username,
                'user_id' => $user->id,
                'avatar_url' => $user->avatar_url
            ]);
        }
        return $userProfile;
    }
}
