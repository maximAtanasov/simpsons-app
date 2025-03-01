<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function findByUsername(string $username): ?User
    {
        return User::where('username', $username)->first();
    }
}
