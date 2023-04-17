<?php

declare(strict_types=1);

namespace Tests\App\Shared\Infrastructure\Persistence\Doctrine;

use App\Shared\Infrastructure\Persistence\Doctrine\Types\DateTimeType;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Exception;
use PHPUnit\Framework\TestCase;
use Throwable;

class DateTimeTypeTest extends TestCase
{
    final public const TYPE = 'test_type';

    final public const BAD_DATE = 'wrong_date';

    private Type $dateTimeType;

    /**
     * @throws Throwable
     */
    public function setUp(): void
    {
        if (! Type::hasType(self::TYPE)) {
            Type::addType(self::TYPE, DateTimeType::class);
        }

        $this->dateTimeType = Type::getType(self::TYPE);
    }

    /**
     * @group unit
     */
    public function testGivenADateTimeTypeWhenIGetTheSqlDeclarationThenItShouldPrintThePlatformString(): void
    {
        self::assertSame('DATETIME', $this->dateTimeType->getSQLDeclaration([], new MySQLPlatform()));
    }

    /**
     * @group unit
     */
    public function testGivenADateTimeTypeWithAInvalidDateThenItShouldThrowAnException(): void
    {
        $this->expectException(ConversionException::class);

        $this->dateTimeType->convertToPHPValue(self::BAD_DATE, new MySQLPlatform());
    }

    /**
     * @test
     *
     * @group unit
     * @throws ConversionException
     */
    public function testGivenADateTimeTypeWithANullDateThenItShouldReturnNull(): void
    {
        self::assertNull($this->dateTimeType->convertToPHPValue(null, new MySQLPlatform()));
    }

    /**
     * @group unit
     */
    public function testGivenAPhpDatetimeValueItShouldThrowAnException(): void
    {
        $this->expectException(ConversionException::class);

        $this->dateTimeType->convertToDatabaseValue(self::BAD_DATE, new MySQLPlatform());
    }

    /**
     * @group unit
     * @throws ConversionException
     */
    public function testGivenAPhpDateTimeTypeWithANullDateThenItShouldReturnNull(): void
    {
        self::assertNull($this->dateTimeType->convertToDatabaseValue(null, new MySQLPlatform()));
    }

    /**
     * @group unit
     *
     * @throws Exception
     */
    public function testGivenAPhpAnImmutableDatetimeValueItShouldReturnACorrectFormat(): void
    {
        $datetimeImmutable = new \DateTimeImmutable();
        $mysqlPlatform = new MySQLPlatform();

        self::assertSame(
            $this->dateTimeType->convertToDatabaseValue($datetimeImmutable, $mysqlPlatform),
            $datetimeImmutable->format($mysqlPlatform->getDateTimeFormatString())
        );
    }
}
