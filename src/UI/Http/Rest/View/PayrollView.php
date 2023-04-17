<?php

declare(strict_types=1);

namespace UI\Http\Rest\View;

use App\Payroll\Domain\Payroll;

final readonly class PayrollView
{
    public function __construct(
        public string $name,
        public string $lastName,
        public string $departmentName,
        public float $remunerationBase,
        public float $additionToBase,
        public string $bonusType,
        public float $salaryWithBonus
    ) {
    }

    public static function fromPayroll(Payroll $payroll): self
    {
        $additionToBase = $payroll->calculateAdditionToBase();

        return new self(
            $payroll->getEmployee()->getFirstName(),
            $payroll->getEmployee()->getLastName(),
            $payroll->getEmployee()->getDepartment()->getName(),
            $payroll->getRemunerationBase()->getMoney()->toFloat(),
            $additionToBase->getAmount()->toFloat(),
            $additionToBase->getAdditionToBaseType()->value,
            $payroll->getTotalSalary()->toFloat()
        );
    }
}
