<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /**
     * Show the welcome page.
     */
    public function welcome(): View
    {
        return view('welcome');
    }
}
