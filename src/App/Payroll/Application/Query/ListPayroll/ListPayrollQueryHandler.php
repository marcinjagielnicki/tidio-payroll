<?php

declare(strict_types=1);

namespace App\Payroll\Application\Query\ListPayroll;

use App\Payroll\Domain\Payroll;
use App\Payroll\Domain\PayrollRepository;
use App\Shared\Application\Query\QueryHandlerInterface;

final readonly class ListPayrollQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private PayrollRepository $payrollRepository
    ) {
    }

    /**
     * @return Payroll[]
     */
    public function __invoke(ListPayrollQuery $listPayrollQuery): array
    {
        return $this->payrollRepository->listPayroll($listPayrollQuery->getPayrollFilter(), $listPayrollQuery->getPayrollSort());
    }
}
