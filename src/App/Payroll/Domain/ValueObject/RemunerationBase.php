<?php

declare(strict_types=1);

namespace App\Payroll\Domain\ValueObject;

use App\Shared\Domain\ValueObject\Money;

readonly class RemunerationBase
{
    public function __construct(
        private Money $money
    ) {
    }

    public function getMoney(): Money
    {
        return $this->money;
    }
}
