<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TicketStatus;
use App\Factory\DateRangeStrategyFactory;
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
        return $this->ticketManager->findTicketById($id);
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getAllTickets(): Collection
    {
        return $this->ticketManager->getAllTickets();
    }

    /**
     * Get ticket statistics for day, week, month, and total
     *
     * @return array{day: int, week: int, month: int, total: int}
     */
    public function getTicketStatistics(): array
    {
        return $this->ticketManager->getTicketStatistics();
    }

    /**
     * Get ticket statistics grouped by month and status
     *
     * @return array<int, array{month: string, year: string, status_counts: array<string, int>}>
     */
    public function getTicketStatisticsByMonthAndStatus(): array
    {
        $tickets = $this->ticketManager->getTicketsGroupedByMonthAndStatus();

        $result = [];
        $monthGroups = $tickets->groupBy('month_year');

        foreach ($monthGroups as $monthYear => $monthTickets) {
            $statusCounts = [];

            foreach ($monthTickets as $ticket) {
                $statusCounts[$ticket['status']] = $ticket['count'];
            }

            $firstTicket = $monthTickets->first();
            $result[] = [
                'month' => $firstTicket['month'] ?? null,
                'year' => $firstTicket['year'] ?? null,
                'month_year' => $monthYear,
                'status_counts' => $statusCounts,
            ];
        }

        return $result;
    }

    /**
     * Get ticket statistics grouped by day from a specified month and status
     *
     * @param  string  $monthSpecifier  The month to get data for (e.g., 'last_month', 'current_month', 'previous_2', etc.)
     * @return array<int, array{day: string, month: string, year: string, status_counts: array<string, int>}>
     */
    public function getTicketStatisticsByDayFromMonth(string $monthSpecifier = 'last_month'): array
    {
        // Use the factory to create the appropriate strategy
        $strategy = DateRangeStrategyFactory::create($monthSpecifier);
        $dateRange = $strategy->getDateRange();

        $monthStart = $dateRange['start'];
        $monthEnd = $dateRange['end'];

        $tickets = $this->ticketManager->getTicketsGroupedByDayFromRange($monthStart->format('Y-m-d H:i:s'), $monthEnd->format('Y-m-d H:i:s'));

        $result = [];
        $dateGroups = $tickets->groupBy('date');

        foreach ($dateGroups as $date => $dateTickets) {
            $statusCounts = [];
            foreach ($dateTickets as $ticket) {
                $statusCounts[$ticket['status']] = $ticket['count'];
            }

            $firstTicket = $dateTickets->first();
            $result[] = [
                'date' => $date,
                'day' => $firstTicket['day'] ?? null,
                'month' => $firstTicket['month'] ?? null,
                'year' => $firstTicket['year'] ?? null,
                'status_counts' => $statusCounts,
            ];
        }

        return $result;
    }

    /**
     * Get ticket count by status
     *
     * @return array<string, int>
     */
    public function getTicketCountByStatus(): array
    {
        $tickets = $this->ticketManager->getTicketsWithStatusCount();

        $result = [];
        foreach ($tickets as $ticket) {
            $result[$ticket['status']] = $ticket['count'];
        }

        // Ensure all possible statuses are present in the result
        $allStatuses = array_column(TicketStatus::cases(), 'value');
        foreach ($allStatuses as $status) {
            if (! isset($result[$status])) {
                $result[$status] = 0;
            }
        }

        return $result;
    }
}
