<?php

declare(strict_types=1);

namespace App\Strategies;

class PreviousMonthsDateRangeStrategy implements DateRangeStrategyInterface
{
    private int $monthsOffset;

    public function __construct(int $monthsOffset)
    {
        $this->monthsOffset = $monthsOffset;
    }

    public function getDateRange(): array
    {
        $start = now()->subMonthsNoOverflow($this->monthsOffset)->startOfMonth();
        $end = now()->subMonthsNoOverflow($this->monthsOffset)->endOfMonth();

        return [
            'start' => $start,
            'end' => $end,
        ];
    }
}
