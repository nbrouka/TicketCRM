<?php

declare(strict_types=1);

namespace App\Services;

use App\Managers\TicketManager;
use App\Models\Ticket;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Collection;

class TicketService
{
    public function __construct(
        protected TicketManager $ticketManager
    ) {}

    /**
     * @return CursorPaginator<int, Ticket>
     */
    public function getFilteredTickets(mixed $request = null): CursorPaginator
    {
        return $this->ticketManager->getFilteredTickets($request);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createTicket(array $data): Ticket
    {
        return $this->ticketManager->createTicket($data);
    }

    public function updateTicketStatus(Ticket $ticket, string $status): bool
    {
        return $this->ticketManager->updateTicketStatus($ticket, $status);
    }

    /**
     * @param  array<string, mixed>  $ticketData
     * @param  array<string, mixed>|null  $customerData
     */
    public function createTicketWithCustomer(array $ticketData, ?array $customerData = null, ?int $customerId = null, mixed $files = null): Ticket
    {
        return $this->ticketManager->createTicketWithCustomer($ticketData, $customerData, $customerId, $files);
    }

    public function findTicketById(int $id): ?Ticket
    {
        return Ticket::with('customer')->find($id);
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getAllTickets(): Collection
    {
        return Ticket::with('customer')->get();
    }
}
