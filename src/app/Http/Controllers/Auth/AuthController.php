<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    public function __construct(
        protected UserService $userService,
    ) {}

    /**
     * Show the login form.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Show the register form.
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = $this->userService->createWebUser([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        auth()->guard('web')->login($user);

        return redirect()->route('dashboard');
    }

    /**
     * Login a user.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $user = $this->userService->authenticateWebUser($request->email, $request->password);

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
