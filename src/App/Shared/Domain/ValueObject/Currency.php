<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use Webmozart\Assert\Assert;

final class Currency
{
    public function __construct(
        public readonly string $currencyCode
    ) {
        Assert::length($currencyCode, 3, 'Currency code must be 3 letters long');
    }

    public function getCode(): string
    {
        return $this->currencyCode;
    }

    public function isSameCurrency(Currency $currency): bool
    {
        return $this->getCode() === $currency->getCode();
    }
}
