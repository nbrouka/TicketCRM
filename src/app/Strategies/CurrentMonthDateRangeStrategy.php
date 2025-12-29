<?php

declare(strict_types=1);

namespace App\Strategies;

class CurrentMonthDateRangeStrategy implements DateRangeStrategyInterface
{
    public function getDateRange(): array
    {
        return [
            'start' => now()->startOfMonth(),
            'end' => now()->endOfMonth(),
        ];
    }
}
