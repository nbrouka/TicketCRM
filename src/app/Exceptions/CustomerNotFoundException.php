<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class CustomerNotFoundException extends TicketException
{
    /**
     * Create a new customer not found exception instance.
     *
     * @param  int|string|null  $customerId
     */
    public function __construct($customerId = null)
    {
        $message = $customerId
            ? "Customer with ID {$customerId} not found."
            : 'Customer not found.';

        parent::__construct($message, Response::HTTP_NOT_FOUND);
    }
}
