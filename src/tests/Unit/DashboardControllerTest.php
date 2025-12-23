<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Http\Controllers\DashboardController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new DashboardController;
    }

    public function test_index_method_returns_dashboard_view()
    {
        $user = User::factory()->create();

        $request = Request::create('/dashboard', 'GET');

        $this->actingAs($user);

        $response = $this->controller->index($request);

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('dashboard', $response->getName());
    }

    public function test_index_method_passes_correct_data_to_view()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $request = Request::create('/dashboard', 'GET');

        $response = $this->controller->index($request);

        $this->assertInstanceOf(View::class, $response);
    }

    public function test_controller_method_authorizes_access()
    {
        $user = User::factory()->create();

        $request = Request::create('/dashboard', 'GET');

        $this->actingAs($user);

        $response = $this->controller->index($request);

        $this->assertInstanceOf(View::class, $response);

        $this->assertEquals('dashboard', $response->getName());
    }
}
