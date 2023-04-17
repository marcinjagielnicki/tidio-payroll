<?php

declare(strict_types=1);

namespace App\Payroll\Domain\ValueObject;

use App\Payroll\Domain\Enum\AdditionToBaseEnum;
use App\Shared\Domain\ValueObject\Money;

readonly class AdditionToBase
{
    public function __construct(
        private Money $amount,
        private AdditionToBaseEnum $additionToBaseType
    ) {
    }

    public function getAdditionToBaseType(): AdditionToBaseEnum
    {
        return $this->additionToBaseType;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }
}
