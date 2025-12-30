<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RateLimitExceededException extends TicketException
{
    protected int $retryAfter;

    public const DEFAULT_RETRY_AFTER = 60;

    /**
     * Create a new rate limit exceeded exception instance.
     *
     * @param  int|null  $retryAfter  Number of seconds to wait before retrying
     */
    public function __construct(string $message = 'Rate limit exceeded. Please try again later.', ?int $retryAfter = null)
    {
        parent::__construct($message, Response::HTTP_TOO_MANY_REQUESTS);
        $this->retryAfter = $retryAfter ?? self::DEFAULT_RETRY_AFTER;
    }

    /**
     * Get the retry after time in seconds.
     */
    public function getRetryAfter(): int
    {
        return $this->retryAfter;
    }

    /**
     * Render the exception as an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function render($request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'retry_after_seconds' => $this->getRetryAfter(),
        ], Response::HTTP_TOO_MANY_REQUESTS);
    }
}
