<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class InvalidTicketStatusException extends TicketException
{
    /**
     * Create a new invalid ticket status exception instance.
     */
    public function __construct(?string $status = null)
    {
        $message = $status
            ? "Invalid ticket status: {$status}"
            : 'Invalid ticket status provided.';

        parent::__construct($message, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
