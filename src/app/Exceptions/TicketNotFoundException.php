<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class TicketNotFoundException extends TicketException
{
    /**
     * Create a new ticket not found exception instance.
     *
     * @param  int|string|null  $ticketId
     */
    public function __construct($ticketId = null)
    {
        $message = $ticketId
            ? "Ticket with ID {$ticketId} not found."
            : 'Ticket not found.';

        parent::__construct($message, Response::HTTP_NOT_FOUND);
    }
}
