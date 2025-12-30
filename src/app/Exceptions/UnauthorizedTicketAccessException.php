<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class UnauthorizedTicketAccessException extends TicketException
{
    /**
     * Create a new unauthorized ticket access exception instance.
     *
     * @param  int|string|null  $ticketId
     */
    public function __construct($ticketId = null)
    {
        $message = $ticketId
            ? "Unauthorized access to ticket with ID {$ticketId}."
            : 'Unauthorized access to ticket.';

        parent::__construct($message, Response::HTTP_FORBIDDEN);
    }
}
