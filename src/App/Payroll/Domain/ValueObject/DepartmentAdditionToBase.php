<?php

declare(strict_types=1);

namespace App\Payroll\Domain\ValueObject;

use App\Payroll\Domain\Enum\AdditionToBaseEnum;
use App\Payroll\Domain\Exception\DepartmentAdditionToBaseInvalidValue;

class DepartmentAdditionToBase
{
    private ?int $amount = null;

    private ?int $percentage = null;

    /**
     * @throws DepartmentAdditionToBaseInvalidValue
     */
    public function __construct(
        private readonly AdditionToBaseEnum $additionToBaseType,
        ?int $amount = null,
        ?int $percentage = null
    ) {
        switch ($this->additionToBaseType) {
            case AdditionToBaseEnum::YEARS_OF_WORKING:
                if (! $amount) {
                    throw new DepartmentAdditionToBaseInvalidValue('Cash amount needs to be specified');
                }
                break;
            case AdditionToBaseEnum::PERCENTAGE:
                if (! $percentage) {
                    throw new DepartmentAdditionToBaseInvalidValue('Percentage needs to be specified');
                }
        }

        $this->amount = $amount;
        $this->percentage = $percentage;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function getPercentage(): ?int
    {
        return $this->percentage;
    }

    public function getAdditionToBaseType(): AdditionToBaseEnum
    {
        return $this->additionToBaseType;
    }
}
