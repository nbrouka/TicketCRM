<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class TicketException extends Exception
{
    /**
     * Create a new ticket exception instance.
     */
    public function __construct(string $message, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
