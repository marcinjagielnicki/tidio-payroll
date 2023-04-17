<?php

declare(strict_types=1);

namespace App\Payroll\Domain\Enum;

enum AdditionToBaseEnum: string
{
    case YEARS_OF_WORKING = 'years_of_working';
    case PERCENTAGE = 'percentage';
}
