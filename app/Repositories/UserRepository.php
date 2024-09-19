<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;

class UserRepository
{
    public function getUserWithProfile($userId)
    {
        return DB::table('users')
            ->leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->where('users.id', $userId)
            ->select('users.*', 'user_profiles.bio', 'user_profiles.profile_picture')
            ->first();
    }

    public function updateUser($userId, $data)
    {
        $updatedUserData = [
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'updated_at' => now(),
        ];

        if (!empty($data['password']) && Hash::check($data['password'], $data['currentPassword'])) {
            $updatedUserData['password'] = Hash::make($data['newpassword']);
        }

        return DB::table('users')->where('id', $userId)->update($updatedUserData);
    }

    public function updateUserProfile($userId, $data)
    {
        $updatedUserProfileData = [
            'bio' => $data['bio'],
            'updated_at' => now(),
        ];

        $userProfile = DB::table('user_profiles')->where('user_id', $userId)->first();

        if ($userProfile) {
            DB::table('user_profiles')->where('user_id', $userId)->update($updatedUserProfileData);
        } else {
            DB::table('user_profiles')->insert([
                ...$updatedUserProfileData,
                'created_at' => now(),
                'user_id' => $userId,
            ]);
        }
    }
}
