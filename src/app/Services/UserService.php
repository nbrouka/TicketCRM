<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    /**
     * Create a new user.
     */
    /**
     * Create a new user.
     *
     * @param  array<string, mixed>  $data
     */
    public function createUser(array $data): User
    {
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ];

        if (isset($data['email_verified_at'])) {
            $userData['email_verified_at'] = $data['email_verified_at'];
        }

        if (isset($data['remember_token'])) {
            $userData['remember_token'] = $data['remember_token'];
        }

        return User::create($userData);
    }

    /**
     * Create a new user for API registration.
     */
    /**
     * Create a new user for API registration.
     *
     * @param  array<string, mixed>  $data
     */
    public function createApiUser(array $data): User
    {
        return $this->createUser($data);
    }

    /**
     * Create a new user for web registration.
     */
    /**
     * Create a new user for web registration.
     *
     * @param  array<string, mixed>  $data
     */
    public function createWebUser(array $data): User
    {
        return $this->createUser($data);
    }

    /**
     * Authenticate user.
     *
     * @throws ValidationException
     */
    public function authenticateUser(string $email, string $password): ?User
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user;
    }

    /**
     * Authenticate user for API.
     *
     * @throws ValidationException
     */
    public function authenticateApiUser(string $email, string $password): User
    {
        return $this->authenticateUser($email, $password);
    }

    /**
     * Authenticate user for web.
     *
     * @throws ValidationException
     */
    public function authenticateWebUser(string $email, string $password): User
    {
        return $this->authenticateUser($email, $password);
    }

    /**
     * Create API token for user.
     */
    public function createApiToken(User $user, string $tokenName = 'API Token'): string
    {
        return $user->createToken($tokenName)->plainTextToken;
    }

    /**
     * Create a new user with API token.
     */
    /**
     * Create a new user with API token.
     *
     * @param  array<string, mixed>  $data
     * @return array{user: \App\Models\User, token: string}
     */
    public function createApiUserWithToken(array $data, string $tokenName = 'API Token'): array
    {
        $user = $this->createUser($data);
        $token = $this->createApiToken($user, $tokenName);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Authenticate user and create API token.
     *
     * @return array{user: \App\Models\User, token: string}
     *
     * @throws ValidationException
     */
    public function authenticateApiUserWithToken(string $email, string $password, string $tokenName = 'API Token'): array
    {
        $user = $this->authenticateUser($email, $password);
        $token = $this->createApiToken($user, $tokenName);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
