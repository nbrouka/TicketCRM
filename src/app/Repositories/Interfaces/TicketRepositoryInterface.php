<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use Illuminate\Support\Collection;

interface TicketRepositoryInterface
{
    /**
     * Get tickets with raw select query
     *
     * @return Collection<int, object>
     */
    public function getTicketsWithStatusCount(): Collection;

    /**
     * Get tickets with raw select query grouped by month
     *
     * @return Collection<int, object>
     */
    public function getTicketsGroupedByMonthAndStatus(): Collection;

    /**
     * Get tickets with raw select query grouped by day
     *
     * @return Collection<int, object>
     */
    public function getTicketsGroupedByDayFromRange(string $startDate, string $endDate): Collection;
}
