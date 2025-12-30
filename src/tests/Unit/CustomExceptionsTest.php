<?php

namespace Tests\Unit;

use App\Exceptions\CustomerNotFoundException;
use App\Exceptions\FileUploadException;
use App\Exceptions\InvalidTicketStatusException;
use App\Exceptions\RateLimitExceededException;
use App\Exceptions\TicketException;
use App\Exceptions\TicketNotFoundException;
use App\Exceptions\UnauthorizedTicketAccessException;
use Illuminate\Http\Response;
use Tests\TestCase;

class CustomExceptionsTest extends TestCase
{
    public function test_ticket_not_found_exception(): void
    {
        $this->expectException(TicketNotFoundException::class);
        $this->expectExceptionMessage('Ticket not found.');
        $this->expectExceptionCode(Response::HTTP_NOT_FOUND);

        throw new TicketNotFoundException;
    }

    public function test_ticket_not_found_exception_with_id(): void
    {
        $this->expectException(TicketNotFoundException::class);
        $this->expectExceptionMessage('Ticket with ID 123 not found.');
        $this->expectExceptionCode(Response::HTTP_NOT_FOUND);

        throw new TicketNotFoundException(123);
    }

    public function test_customer_not_found_exception(): void
    {
        $this->expectException(CustomerNotFoundException::class);
        $this->expectExceptionMessage('Customer not found.');
        $this->expectExceptionCode(Response::HTTP_NOT_FOUND);

        throw new CustomerNotFoundException;
    }

    public function test_customer_not_found_exception_with_id(): void
    {
        $this->expectException(CustomerNotFoundException::class);
        $this->expectExceptionMessage('Customer with ID 456 not found.');
        $this->expectExceptionCode(Response::HTTP_NOT_FOUND);

        throw new CustomerNotFoundException(456);
    }

    public function test_invalid_ticket_status_exception(): void
    {
        $this->expectException(InvalidTicketStatusException::class);
        $this->expectExceptionMessage('Invalid ticket status provided.');
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);

        throw new InvalidTicketStatusException;
    }

    public function test_invalid_ticket_status_exception_with_status(): void
    {
        $this->expectException(InvalidTicketStatusException::class);
        $this->expectExceptionMessage('Invalid ticket status: invalid_status');
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);

        throw new InvalidTicketStatusException('invalid_status');
    }

    public function test_unauthorized_ticket_access_exception(): void
    {
        $this->expectException(UnauthorizedTicketAccessException::class);
        $this->expectExceptionMessage('Unauthorized access to ticket.');
        $this->expectExceptionCode(Response::HTTP_FORBIDDEN);

        throw new UnauthorizedTicketAccessException;
    }

    public function test_unauthorized_ticket_access_exception_with_id(): void
    {
        $this->expectException(UnauthorizedTicketAccessException::class);
        $this->expectExceptionMessage('Unauthorized access to ticket with ID 789.');
        $this->expectExceptionCode(Response::HTTP_FORBIDDEN);

        throw new UnauthorizedTicketAccessException(789);
    }

    public function test_rate_limit_exceeded_exception(): void
    {
        $exception = new RateLimitExceededException;
        $this->assertEquals('Rate limit exceeded. Please try again later.', $exception->getMessage());
        $this->assertEquals(Response::HTTP_TOO_MANY_REQUESTS, $exception->getCode());
        $this->assertEquals(RateLimitExceededException::DEFAULT_RETRY_AFTER, $exception->getRetryAfter());
    }

    public function test_rate_limit_exceeded_exception_with_custom_message_and_retry(): void
    {
        $exception = new RateLimitExceededException('Too many requests', 120);
        $this->assertEquals('Too many requests', $exception->getMessage());
        $this->assertEquals(Response::HTTP_TOO_MANY_REQUESTS, $exception->getCode());
        $this->assertEquals(120, $exception->getRetryAfter());
    }

    public function test_file_upload_exception(): void
    {
        $this->expectException(FileUploadException::class);
        $this->expectExceptionMessage('File upload failed.');
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);

        throw new FileUploadException;
    }

    public function test_file_upload_exception_with_custom_message(): void
    {
        $this->expectException(FileUploadException::class);
        $this->expectExceptionMessage('File size too large');
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);

        throw new FileUploadException('File size too large');
    }

    public function test_ticket_exception_base_functionality(): void
    {
        $this->expectException(TicketException::class);
        $this->expectExceptionMessage('Test ticket exception message');
        $this->expectExceptionCode(50);

        throw new TicketException('Test ticket exception message', 50);
    }
}
