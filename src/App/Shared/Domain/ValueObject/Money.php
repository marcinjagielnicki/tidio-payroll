<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidCurrencyException;

class Money
{
    final public function __construct(
        protected readonly int $amount,
        protected readonly Currency $currency
    ) {
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @throws InvalidCurrencyException
     */
    public function add(self $money): static
    {
        if (! $money->getCurrency()->isSameCurrency($this->getCurrency())) {
            throw new InvalidCurrencyException('Currency not equal');
        }

        return new static($this->getAmount() + $money->getAmount(), new Currency($this->currency->getCode()));
    }

    // Simple toFloat function. Can be extended with different currencies handling
    public function toFloat(): float
    {
        return $this->amount / 100;
    }
}
