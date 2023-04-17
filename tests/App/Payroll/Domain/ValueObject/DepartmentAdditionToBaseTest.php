<?php

declare(strict_types=1);

namespace Tests\App\Payroll\Domain\ValueObject;

use App\Payroll\Domain\Enum\AdditionToBaseEnum;
use App\Payroll\Domain\Exception\DepartmentAdditionToBaseInvalidValue;
use App\Payroll\Domain\ValueObject\DepartmentAdditionToBase;
use PHPUnit\Framework\TestCase;

class DepartmentAdditionToBaseTest extends TestCase
{
    /**
     * @group unit
     * @throws \App\Payroll\Domain\Exception\DepartmentAdditionToBaseInvalidValue
     */
    public function testGivenYearsOfWorkingTypeAndAmount_CreatesObjectWithCorrectValues(): void
    {
        $additionToBaseType = AdditionToBaseEnum::YEARS_OF_WORKING;
        $amount = 100;

        $departmentAdditionToBase = new DepartmentAdditionToBase($additionToBaseType, $amount);

        $this->assertEquals($amount, $departmentAdditionToBase->getAmount());
        $this->assertNull($departmentAdditionToBase->getPercentage());
        $this->assertEquals($additionToBaseType, $departmentAdditionToBase->getAdditionToBaseType());
    }

    /**
     * @group unit
     * @throws \App\Payroll\Domain\Exception\DepartmentAdditionToBaseInvalidValue
     */
    public function testGivenPercentageTypeAndPercentage_CreatesObjectWithCorrectValues(): void
    {
        $additionToBaseType = AdditionToBaseEnum::PERCENTAGE;
        $percentage = 5;

        $departmentAdditionToBase = new DepartmentAdditionToBase($additionToBaseType, null, $percentage);

        $this->assertNull($departmentAdditionToBase->getAmount());
        $this->assertEquals($percentage, $departmentAdditionToBase->getPercentage());
        $this->assertEquals($additionToBaseType, $departmentAdditionToBase->getAdditionToBaseType());
    }

    /**
     * @group unit
     * @throws DepartmentAdditionToBaseInvalidValue
     */
    public function testGivenYearsOfWorkingTypeWithoutAmount_ThrowsInvalidValueException(): void
    {
        $this->expectException(DepartmentAdditionToBaseInvalidValue::class);
        $this->expectExceptionMessage('Cash amount needs to be specified');

        $additionToBaseType = AdditionToBaseEnum::YEARS_OF_WORKING;

        new DepartmentAdditionToBase($additionToBaseType, null, 50);
    }

    /**
     * @group unit
     * @throws DepartmentAdditionToBaseInvalidValue
     */
    public function testGivenPercentageTypeWithoutPercentage_ThrowsInvalidValueException(): void
    {
        $this->expectException(DepartmentAdditionToBaseInvalidValue::class);
        $this->expectExceptionMessage('Percentage needs to be specified');

        $additionToBaseType = AdditionToBaseEnum::PERCENTAGE;

        new DepartmentAdditionToBase($additionToBaseType, 50);
    }
}
