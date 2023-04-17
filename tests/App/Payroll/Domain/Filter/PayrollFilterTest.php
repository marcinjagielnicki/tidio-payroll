<?php

declare(strict_types=1);

namespace Tests\App\Payroll\Domain\Filter;

use App\Payroll\Domain\Filter\PayrollFilter;
use App\Shared\Domain\Exception\FilterNotValidException;
use App\Shared\Domain\Filter\StringFilter;
use App\Shared\Domain\Filter\UuidFilter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class PayrollFilterTest extends TestCase
{
    /**
     * @group unit
     * @throws FilterNotValidException
     */
    public function testValidFilters_Passes(): void
    {
        $uuidFilter = new UuidFilter('uuid', (string) Uuid::v4());
        $firstNameFilter = new StringFilter('firstName', 'John');
        $lastNameFilter = new StringFilter('lastName', 'Doe');

        $this->expectNotToPerformAssertions();

        new PayrollFilter($uuidFilter);
        new PayrollFilter($firstNameFilter);
        new PayrollFilter($lastNameFilter);
    }

    /**
     * @group unit
     * @throws FilterNotValidException
     */
    public function testInvalidFilterColumnName_ThrowsException(): void
    {
        $this->expectException(FilterNotValidException::class);
        new PayrollFilter(new StringFilter('invalidColumnName', 'John'));
    }

    /**
     * @group unit
     * @throws FilterNotValidException
     */
    public function testInvalidFilterType_ThrowsException(): void
    {
        $this->expectException(FilterNotValidException::class);
        new PayrollFilter(new UuidFilter('firstName', (string) Uuid::v4()));
    }

    /**
     * @group unit
     * @throws FilterNotValidException
     */
    public function testCreateFromColumnNameAndValue_Passes(): void
    {
        $uuid = Uuid::v4();
        $payrollFilter1 = PayrollFilter::createFromColumnNameAndValue('uuid', (string) $uuid);
        $payrollFilter2 = PayrollFilter::createFromColumnNameAndValue('firstName', 'John');
        $payrollFilter3 = PayrollFilter::createFromColumnNameAndValue('lastName', 'Doe');

        $this->assertInstanceOf(UuidFilter::class, $payrollFilter1->getFilter());
        $this->assertInstanceOf(StringFilter::class, $payrollFilter2->getFilter());
        $this->assertInstanceOf(StringFilter::class, $payrollFilter3->getFilter());
    }

    /**
     * @group unit
     * @throws FilterNotValidException
     */
    public function testCreateFromInvalidColumnNameAndValue_ThrowsException(): void
    {
        $this->expectException(FilterNotValidException::class);
        PayrollFilter::createFromColumnNameAndValue('invalidColumnName', 'John');
    }
}
