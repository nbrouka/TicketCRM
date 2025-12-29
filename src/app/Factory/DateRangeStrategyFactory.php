<?php

declare(strict_types=1);

namespace App\Factory;

use App\Strategies\CurrentMonthDateRangeStrategy;
use App\Strategies\DateRangeStrategyInterface;
use App\Strategies\PreviousMonthsDateRangeStrategy;

class DateRangeStrategyFactory
{
    public const CURRENT_MONTH = 'current_month';

    public const PREVIOUS_2 = 'previous_2';

    public const PREVIOUS_3 = 'previous_3';

    public const PREVIOUS_4 = 'previous_4';

    public const PREVIOUS_5 = 'previous_5';

    public const PREVIOUS_6 = 'previous_6';

    public const LAST_MONTH = 'last_month';

    public const ONE_MONTH_AGO = 1;

    public const TWO_MONTHS_AGO = 2;

    public const THREE_MONTHS_AGO = 3;

    public const FOUR_MONTHS_AGO = 4;

    public const FIVE_MONTHS_AGO = 5;

    public const SIX_MONTHS_AGO = 6;

    public static function create(string $monthSpecifier): DateRangeStrategyInterface
    {
        switch ($monthSpecifier) {
            case self::CURRENT_MONTH:
                return new CurrentMonthDateRangeStrategy;
            case self::PREVIOUS_2:
                return new PreviousMonthsDateRangeStrategy(self::TWO_MONTHS_AGO);
            case self::PREVIOUS_3:
                return new PreviousMonthsDateRangeStrategy(self::THREE_MONTHS_AGO);
            case self::PREVIOUS_4:
                return new PreviousMonthsDateRangeStrategy(self::FOUR_MONTHS_AGO);
            case self::PREVIOUS_5:
                return new PreviousMonthsDateRangeStrategy(self::FIVE_MONTHS_AGO);
            case self::PREVIOUS_6:
                return new PreviousMonthsDateRangeStrategy(self::SIX_MONTHS_AGO);
            case self::LAST_MONTH:
            default:
                return new PreviousMonthsDateRangeStrategy(self::ONE_MONTH_AGO);
        }
    }
}
