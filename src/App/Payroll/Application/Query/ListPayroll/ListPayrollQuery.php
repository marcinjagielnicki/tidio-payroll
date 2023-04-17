<?php

declare(strict_types=1);

namespace App\Payroll\Application\Query\ListPayroll;

use App\Payroll\Domain\Filter\PayrollFilter;
use App\Payroll\Domain\Sort\PayrollSort;
use App\Shared\Application\Query\QueryInterface;

final readonly class ListPayrollQuery implements QueryInterface
{
    public function __construct(
        private ?PayrollSort $payrollSort = null,
        private ?PayrollFilter $payrollFilter = null
    ) {
    }

    public function getPayrollSort(): ?PayrollSort
    {
        return $this->payrollSort;
    }

    public function getPayrollFilter(): ?PayrollFilter
    {
        return $this->payrollFilter;
    }
}
