<?php

declare(strict_types=1);

namespace App\Payroll\Domain;

use App\Payroll\Domain\ValueObject\AdditionToBase;
use App\Payroll\Domain\ValueObject\RemunerationBase;
use App\Payroll\Domain\ValueObject\TotalSalary;
use App\Shared\Domain\AggregateRoot;
use App\Shared\Domain\ValueObject\Currency;
use App\Shared\Domain\ValueObject\Money;

class Payroll extends AggregateRoot
{
    public function __construct(
        private readonly Employee $employee,
        private readonly RemunerationBase $remunerationBase
    ) {
    }

    public function getEmployee(): Employee
    {
        return $this->employee;
    }

    public function getRemunerationBase(): RemunerationBase
    {
        return $this->remunerationBase;
    }

    public function calculateAdditionToBase(): AdditionToBase
    {
        // For seniority grater than 10 years we count only fist 10 years
        $seniority = $this->employee->getSeniorityInYears() > 10 ? 10 : $this->employee->getSeniorityInYears();

        $additionAmount = match ($this->employee->getDepartment()->getAdditionToBase()->getAdditionToBaseType()) {
            Enum\AdditionToBaseEnum::YEARS_OF_WORKING => $seniority * $this->employee->getDepartment()->getAdditionToBase()->getAmount(),
            Enum\AdditionToBaseEnum::PERCENTAGE => $this->remunerationBase->getMoney()->getAmount() * ($this->employee->getDepartment()->getAdditionToBase()->getPercentage() / 100),
        };

        return new AdditionToBase(
            new Money(
                (int) round($additionAmount),
                $this->remunerationBase->getMoney()->getCurrency(),
            ),
            $this->employee->getDepartment()->getAdditionToBase()->getAdditionToBaseType()
        );
    }

    public function getTotalSalary(): TotalSalary
    {
        $totalSalary = new TotalSalary($this->getRemunerationBase()->getMoney()->getAmount(), new Currency($this->getRemunerationBase()->getMoney()->getCurrency()->getCode()));
        $additionToBase = $this->calculateAdditionToBase();

        return $totalSalary->add($additionToBase->getAmount());
    }
}
