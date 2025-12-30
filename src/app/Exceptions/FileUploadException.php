<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class FileUploadException extends TicketException
{
    /**
     * Create a new file upload exception instance.
     */
    public function __construct(string $message = 'File upload failed.')
    {
        parent::__construct($message, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
