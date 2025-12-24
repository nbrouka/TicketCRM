<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        auth()->guard('web')->login($user);

        return redirect()->route('dashboard');
    }

    /**
     * Login a user.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        auth()->guard('web')->login($user);

        return redirect()->route('dashboard');
    }

    /**
     * Logout the authenticated user.
     */
    public function logout(): RedirectResponse
    {
        auth()->guard('web')->logout();

        return redirect()->route('login');
    }
}
