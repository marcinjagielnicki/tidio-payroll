<?php

declare(strict_types=1);

namespace Tests\App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidCurrencyException;
use App\Shared\Domain\ValueObject\Currency;
use App\Shared\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    /**
     * @group unit
     */
    public function testGivenAmountAndCurrency_GetAmountReturnsCorrectValue(): void
    {
        $currency = new Currency('USD');
        $money = new Money(100, $currency);

        $this->assertEquals(100, $money->getAmount());
    }

    /**
     * @group unit
     */
    public function testGivenAmountAndCurrency_GetCurrencyReturnsCorrectValue(): void
    {
        $currency = new Currency('USD');
        $money = new Money(100, $currency);

        $this->assertEquals($currency, $money->getCurrency());
    }

    /**
     * @group unit
     * @throws InvalidCurrencyException
     */
    public function testGivenSameCurrencyAmounts_AddReturnsCorrectMoneyObject(): void
    {
        $currencyUSD = new Currency('USD');
        $money1 = new Money(100, $currencyUSD);
        $money2 = new Money(200, $currencyUSD);

        $result = $money1->add($money2);

        $this->assertEquals(300, $result->getAmount());
        $this->assertEquals($currencyUSD, $result->getCurrency());
    }

    /**
     * @group unit
     */
    public function testGivenDifferentCurrencyAmounts_AddThrowsInvalidCurrencyException(): void
    {
        $this->expectException(InvalidCurrencyException::class);
        $this->expectExceptionMessage('Currency not equal');

        $currencyUSD = new Currency('USD');
        $currencyEUR = new Currency('EUR');
        $money1 = new Money(100, $currencyUSD);
        $money2 = new Money(200, $currencyEUR);

        $money1->add($money2);
    }
}
