<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class FeedbackController extends Controller
{
    /**
     * Show the feedback widget form.
     */
    public function show(): View
    {
        return view('feedback-widget');
    }

    /**
     * Show the feedback demo page.
     */
    public function demo(): View
    {
        return view('feedback-demo');
    }
}
