<?php

declare(strict_types=1);

namespace Tests\App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\DateTimeException;
use App\Shared\Domain\ValueObject\DateTime;
use PHPUnit\Framework\TestCase;

class DateTimeTest extends TestCase
{
    final public const WRONG_DATE = 'WRONG_DATE_LOL';

    /**
     * @group unit
     */
    public function testGivenABadFormattedDateTimeStringItShouldThrownAnExceptionWhenDateTimeIsCreated(): void
    {
        $this->expectException(DateTimeException::class);
        DateTime::fromString(self::WRONG_DATE);
    }
}
