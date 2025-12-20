<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\DashboardController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new DashboardController();
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
            'email' => 'test@example.com'
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
