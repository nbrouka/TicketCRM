<?php

namespace App\Exceptions;

use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExceptionRegistrar
{
    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(Exceptions $exceptions): void
    {
        $exceptions->render(function (RateLimitExceededException $e, Request $request) {
            return $e->render($request);
        });

        $exceptions->render(function (TicketException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return response()->view('errors.'.$e->getCode(), [], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        });
    }
}
