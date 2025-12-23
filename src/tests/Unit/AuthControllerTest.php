<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new AuthController;
    }

    public function test_register_method_creates_user()
    {
        $request = new RegisterRequest;
        $request->merge([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response = $this->controller->register($request);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertTrue(Hash::check('password', $user->password));
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringEndsWith('/dashboard', $response->getTargetUrl());
    }

    public function test_login_method_authenticates_user()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $request = new LoginRequest;
        $request->merge([
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response = $this->controller->login($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringEndsWith('/dashboard', $response->getTargetUrl());
    }

    public function test_login_method_returns_back_with_error_for_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $request = new LoginRequest;
        $request->merge([
            'email' => 'test@example.com',
            'password' => 'wrong_password',
        ]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The provided credentials are incorrect.');

        $this->controller->login($request);
    }

    public function test_logout_method_logs_out_user()
    {
        $user = User::factory()->create();

        $request = new Request;
        $request->setLaravelSession($this->app['session']->driver());

        $this->app['session']->driver()->put('login_'.$this->app['auth']->getDefaultDriver().'_'.$user->id, $user->id);
        $this->app['auth']->login($user);

        $response = $this->controller->logout($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringEndsWith('/login', $response->getTargetUrl());
    }

    public function test_register_method_fails_with_missing_fields()
    {
        $request = new RegisterRequest;
        $request->setContainer(app())
            ->setRedirector(app('redirect'))
            ->merge([
                'name' => '',
                'email' => 'invalid-email',
                'password' => '',
                'password_confirmation' => '',
            ]);

        $rules = $request->rules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('password', $rules);

        $nameRules = is_string($rules['name']) ? explode('|', $rules['name']) : $rules['name'];
        $emailRules = is_string($rules['email']) ? explode('|', $rules['email']) : $rules['email'];
        $passwordRules = is_string($rules['password']) ? explode('|', $rules['password']) : $rules['password'];

        $this->assertContains('required', $nameRules);
        $this->assertContains('required', $emailRules);
        $this->assertContains('required', $passwordRules);
    }
}
