<?php

declare(strict_types=1);

namespace App\Managers;

use App\Filters\TicketFilter;
use App\Models\Customer;
use App\Models\Ticket;
use InvalidArgumentException;

class TicketManager
{
    const DEFAULT_PER_PAGE = 15;

    public function getFilteredTickets($request = null)
    {
        $filter = new TicketFilter($request);

        // Apply filters
        $query = Ticket::with(['customer:id,name,email,phone', 'media'])
            ->filter($filter);

        // Sort by created_at desc and id desc by default (important for cursor pagination)
        $query->orderBy('created_at', 'desc')->orderBy('id', 'desc');

        return $query->cursorPaginate(self::DEFAULT_PER_PAGE)->withQueryString();
    }

    public function createTicket(array $data)
    {
        return Ticket::create($data);
    }

    public function updateTicketStatus(Ticket $ticket, string $status)
    {
        return $ticket->update([
            'status' => $status,
        ]);
    }

    public function createTicketWithCustomer(array $ticketData, ?array $customerData = null, ?int $customerId = null, $files = null)
    {
        $ticket = $this->createTicketForCustomer($ticketData, $customerData, $customerId);

        // Attach files if provided
        if ($files) {
            $ticket->attachFiles($files);
        }

        return $ticket;
    }

    private function createTicketForCustomer(array $ticketData, ?array $customerData, ?int $customerId): Ticket
    {
        if ($customerId) {
            return Ticket::create(array_merge($ticketData, [
                'customer_id' => $customerId,
            ]));
        }

        if (! $customerData) {
            throw new InvalidArgumentException('Either customerData or customerId must be provided');
        }

        $customer = $this->findOrCreateCustomer($customerData);

        return Ticket::create(array_merge($ticketData, [
            'customer_id' => $customer->id,
        ]));
    }

    private function findOrCreateCustomer(array $customerData): Customer
    {
        $customer = Customer::where('email', $customerData['email'])
            ->orWhere('phone', $customerData['phone'])
            ->first();

        if (! $customer) {
            $customer = Customer::create($customerData);
        }

        return $customer;
    }
}
