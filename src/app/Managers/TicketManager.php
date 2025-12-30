<?php

declare(strict_types=1);

namespace App\Managers;

use App\Exceptions\TicketException;
use App\Filters\TicketFilter;
use App\Models\Customer;
use App\Models\Ticket;
use App\Repositories\Interfaces\TicketRepositoryInterface;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Collection;

class TicketManager
{
    const DEFAULT_PER_PAGE = 15;

    public function __construct(
        protected TicketRepositoryInterface $ticketRepository
    ) {}

    /**
     * @return CursorPaginator<int, Ticket>
     */
    public function getFilteredTickets(mixed $request = null): CursorPaginator
    {
        $filter = new TicketFilter($request);

        // Get limit from request, default to DEFAULT_PER_PAGE, max 100
        $limit = $request?->input('limit', self::DEFAULT_PER_PAGE);
        // Ensure limit is between 1 and 100
        $limit = min(max((int) $limit, 1), 100);
        // Apply filters
        $query = Ticket::with(['customer:id,name,email,phone', 'media'])
            ->filter($filter);

        // Sort by created_at desc and id desc by default (important for cursor pagination)
        $query->orderBy('created_at', 'desc')->orderBy('id', 'desc');

        return $query->cursorPaginate($limit)->withQueryString();
    }

    /** @param array<string, mixed> $data */
    public function createTicket(array $data): Ticket
    {
        return Ticket::create($data);
    }

    public function updateTicketStatus(Ticket $ticket, string $status): bool
    {
        return $ticket->update([
            'status' => $status,
        ]);
    }

    /**
     * @param  array<string, mixed>  $ticketData
     * @param  array<string, mixed>|null  $customerData
     */
    public function createTicketWithCustomer(array $ticketData, ?array $customerData = null, ?int $customerId = null, mixed $files = null): Ticket
    {
        $ticket = $this->createTicketForCustomer($ticketData, $customerData, $customerId);

        // Attach files if provided
        if ($files) {
            $ticket->attachFiles($files);
        }

        return $ticket;
    }

    /**
     * Find ticket by ID
     */
    public function findTicketById(int $id): ?Ticket
    {
        return Ticket::with('customer')->find($id);
    }

    /**
     * Get all tickets
     *
     * @return Collection<int, Ticket>
     */
    public function getAllTickets(): Collection
    {
        return Ticket::with('customer')->get();
    }

    /**
     * Get ticket statistics for day, week, month, and total
     *
     * @return array{day: int, week: int, month: int, total: int}
     */
    public function getTicketStatistics(): array
    {
        return [
            'day' => Ticket::today()->count(),
            'week' => Ticket::thisWeek()->count(),
            'month' => Ticket::thisMonth()->count(),
            'total' => Ticket::count(),
        ];
    }

    /**
     * Get tickets with raw select query
     *
     * @return Collection<int, object>
     */
    public function getTicketsWithStatusCount(): Collection
    {
        return $this->ticketRepository->getTicketsWithStatusCount();
    }

    /**
     * Get tickets with raw select query grouped by month
     *
     * @return Collection<int, object>
     */
    public function getTicketsGroupedByMonthAndStatus(): Collection
    {
        return $this->ticketRepository->getTicketsGroupedByMonthAndStatus();
    }

    /**
     * Get tickets with raw select query grouped by day
     *
     * @return Collection<int, object>
     */
    public function getTicketsGroupedByDayFromRange(string $startDate, string $endDate): Collection
    {
        return $this->ticketRepository->getTicketsGroupedByDayFromRange($startDate, $endDate);
    }

    /**
     * @param  array<string, mixed>  $ticketData
     * @param  array<string, mixed>|null  $customerData
     */
    private function createTicketForCustomer(array $ticketData, ?array $customerData, ?int $customerId): Ticket
    {
        if ($customerId) {
            return Ticket::create(array_merge($ticketData, [
                'customer_id' => $customerId,
            ]));
        }

        if (! $customerData) {
            throw new TicketException('Either customerData or customerId must be provided');
        }

        $customer = $this->findOrCreateCustomer($customerData);

        return Ticket::create(array_merge($ticketData, [
            'customer_id' => $customer->id,
        ]));
    }

    /** @param array<string, mixed> $customerData */
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
