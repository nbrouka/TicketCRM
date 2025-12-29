<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Ticket;
use App\Repositories\Interfaces\TicketRepositoryInterface;
use Illuminate\Support\Collection;

class TicketRepository implements TicketRepositoryInterface
{
    /**
     * Get tickets with raw select query
     *
     * @return Collection<int, object>
     */
    public function getTicketsWithStatusCount(): Collection
    {
        return collect(
            Ticket::selectRaw('CAST(status AS CHAR) as status, COUNT(*) as count')
                ->groupBy('status')
                ->get()
                ->toArray()
        );
    }

    /**
     * Get tickets with raw select query grouped by month
     *
     * @return Collection<int, object>
     */
    public function getTicketsGroupedByMonthAndStatus(): Collection
    {
        return collect(
            Ticket::selectRaw(
                "DATE_FORMAT(created_at, '%Y-%m') as month_year,
                 DATE_FORMAT(created_at, '%Y') as year,
                 DATE_FORMAT(created_at, '%c') as month,
                 CAST(status AS CHAR) as status,
                 COUNT(*) as count"
            )
                ->groupByRaw("DATE_FORMAT(created_at, '%Y-%m'), DATE_FORMAT(created_at, '%Y'), DATE_FORMAT(created_at, '%c'), status")
                ->orderByRaw("DATE_FORMAT(created_at, '%Y-%m')")
                ->get()
                ->toArray()
        );
    }

    /**
     * Get tickets with raw select query grouped by day
     *
     * @return Collection<int, object>
     */
    public function getTicketsGroupedByDayFromRange(string $startDate, string $endDate): Collection
    {
        return collect(
            Ticket::selectRaw(
                "DATE_FORMAT(created_at, '%Y-%m-%d') as date,
                 DATE_FORMAT(created_at, '%e') as day,
                 DATE_FORMAT(created_at, '%c') as month,
                 DATE_FORMAT(created_at, '%Y') as year,
                 CAST(status AS CHAR) as status,
                 COUNT(*) as count"
            )
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupByRaw("DATE_FORMAT(created_at, '%Y-%m-%d'), DATE_FORMAT(created_at, '%e'), DATE_FORMAT(created_at, '%c'), DATE_FORMAT(created_at, '%Y'), status")
                ->orderByRaw("DATE_FORMAT(created_at, '%Y-%m-%d')")
                ->get()
                ->toArray()
        );
    }
}
