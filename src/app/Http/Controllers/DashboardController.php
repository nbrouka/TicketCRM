<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard view.
     */
    public function index(): View
    {
        return view('dashboard');
    }
}
