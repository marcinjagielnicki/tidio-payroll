<?php

declare(strict_types=1);

namespace App\Payroll\Domain;

use App\Payroll\Domain\Filter\PayrollFilter;
use App\Payroll\Domain\Sort\PayrollSort;

interface PayrollRepository
{
    /**
     * @return Payroll[]
     */
    public function listPayroll(?PayrollFilter $filter = null, ?PayrollSort $payrollSort = null): array;
}
