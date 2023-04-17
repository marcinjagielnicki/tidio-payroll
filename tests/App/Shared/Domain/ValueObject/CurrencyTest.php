<?php

declare(strict_types=1);

namespace Tests\App\Shared\Domain\ValueObject;

use App\Shared\Domain\ValueObject\Currency;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

class CurrencyTest extends TestCase
{
    /**
     * @group unit
     */
    public function testGivenACurrencyNameDifferentThanExpectedItShouldFailWhileCreatingObject(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Currency('Polski Zloty');
    }

    /**
     * @group unit
     */
    public function testGivenATwoSameCurrenciesItShouldReturnTrueWhenComparingThem(): void
    {
        $usd = new Currency('USD');
        $usd2 = new Currency('USD');

        $this->assertTrue($usd->isSameCurrency($usd2));
    }

    /**
     * @group unit
     */
    public function testGivenATwoDifferentCurrenciesItShouldReturnFalseWhenComparingThem(): void
    {
        $usd = new Currency('USD');
        $usd2 = new Currency('PLN');

        $this->assertFalse($usd->isSameCurrency($usd2));
    }

    /**
     * @group unit
     */
    public function testGivenACurrencyNameItShouldReturnSameNameInGetCodeMethod(): void
    {
        $usd = new Currency('USD');
        $this->assertSame('USD', $usd->getCode());
    }
}
