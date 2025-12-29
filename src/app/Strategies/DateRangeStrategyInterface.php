<?php

declare(strict_types=1);

namespace App\Strategies;

use DateTime;

interface DateRangeStrategyInterface
{
    /**
     * Get the start and end date for the specified range
     *
     * @return array{start: DateTime, end: DateTime}
     */
    public function getDateRange(): array;
}
