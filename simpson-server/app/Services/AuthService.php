<?php

namespace App\Services;

use App\Exceptions\InvalidCredentialsException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * @param string $username The username, must not be null or empty.
     * @param string $password The password, must not be null or empty.
     * @return string A new access token for the given user. The valid duration of the token is indefinite.
     * @throws InvalidCredentialsException when the given credentials are invalid.
     */
    public function login(string $username, string $password): string
    {
        $user = User::where('username', $username)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw new InvalidCredentialsException('Invalid credentials');
        }

        return $user->createToken('auth_token')->plainTextToken;
    }
}
