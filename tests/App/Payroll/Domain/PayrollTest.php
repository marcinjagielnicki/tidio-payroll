<?php

declare(strict_types=1);

namespace Tests\App\Payroll\Domain;

use App\Payroll\Domain\Department;
use App\Payroll\Domain\Employee;
use App\Payroll\Domain\Enum\AdditionToBaseEnum;
use App\Payroll\Domain\Exception\DepartmentAdditionToBaseInvalidValue;
use App\Payroll\Domain\Payroll;
use App\Payroll\Domain\ValueObject\DepartmentAdditionToBase;
use App\Payroll\Domain\ValueObject\RemunerationBase;
use App\Shared\Domain\ValueObject\Currency;
use App\Shared\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;

class PayrollTest extends TestCase
{
    private function createEmployeeMock(int $seniorityInYears, DepartmentAdditionToBase $departmentAdditionToBase): \PHPUnit\Framework\MockObject\MockObject&Employee
    {
        $departmentMock = $this->getMockBuilder(Department::class)
            ->disableOriginalConstructor()
            ->getMock();
        $departmentMock->method('getAdditionToBase')->willReturn($departmentAdditionToBase);

        $employeeMock = $this->getMockBuilder(Employee::class)
            ->disableOriginalConstructor()
            ->getMock();
        $employeeMock->method('getSeniorityInYears')->willReturn($seniorityInYears);
        $employeeMock->method('getDepartment')->willReturn($departmentMock);

        return $employeeMock;
    }

    /**
     * @group unit
     * @throws DepartmentAdditionToBaseInvalidValue
     */
    public function testGivenEmployeeAndRemunerationBase_CalculateAdditionToBaseCorrectly(): void
    {
        $seniorityInYears = 8;
        $additionToBaseType = AdditionToBaseEnum::YEARS_OF_WORKING;
        $amount = 50;
        $departmentAdditionToBase = new DepartmentAdditionToBase($additionToBaseType, $amount);
        $employeeMock = $this->createEmployeeMock($seniorityInYears, $departmentAdditionToBase);

        $remunerationBase = new RemunerationBase(new Money(2000, new Currency('USD')));
        $payroll = new Payroll($employeeMock, $remunerationBase);

        $additionToBase = $payroll->calculateAdditionToBase();

        $this->assertEquals($seniorityInYears * $amount, $additionToBase->getAmount()->getAmount());
        $this->assertEquals($remunerationBase->getMoney()->getCurrency(), $additionToBase->getAmount()->getCurrency());
        $this->assertEquals($additionToBaseType, $additionToBase->getAdditionToBaseType());
    }

    /**
     * @group unit
     * @throws DepartmentAdditionToBaseInvalidValue
     */
    public function testGivenEmployeeWithOverTenYearsOfSeniorityAndRemunerationBase_CalculateAdditionToBaseCorrectly(): void
    {
        $seniorityInYears = 14;
        $additionToBaseType = AdditionToBaseEnum::YEARS_OF_WORKING;
        $amount = 50;
        $departmentAdditionToBase = new DepartmentAdditionToBase($additionToBaseType, $amount);
        $employeeMock = $this->createEmployeeMock($seniorityInYears, $departmentAdditionToBase);

        $remunerationBase = new RemunerationBase(new Money(2000, new Currency('USD')));
        $payroll = new Payroll($employeeMock, $remunerationBase);

        $additionToBase = $payroll->calculateAdditionToBase();

        // Should calculate first 10 years
        $this->assertEquals(10 * $amount, $additionToBase->getAmount()->getAmount());
        $this->assertEquals($remunerationBase->getMoney()->getCurrency(), $additionToBase->getAmount()->getCurrency());
        $this->assertEquals($additionToBaseType, $additionToBase->getAdditionToBaseType());
    }

    /**
     * @group unit
     * @throws DepartmentAdditionToBaseInvalidValue
     */
    public function testGivenEmployeeAndRemunerationBase_GetTotalSalaryCorrectly(): void
    {
        $seniorityInYears = 8;
        $additionToBaseType = AdditionToBaseEnum::YEARS_OF_WORKING;
        $amount = 50;
        $departmentAdditionToBase = new DepartmentAdditionToBase($additionToBaseType, $amount);
        $employeeMock = $this->createEmployeeMock($seniorityInYears, $departmentAdditionToBase);

        $remunerationBase = new RemunerationBase(new Money(2000, new Currency('USD')));
        $payroll = new Payroll($employeeMock, $remunerationBase);

        $totalSalary = $payroll->getTotalSalary();

        $expectedAmount = $remunerationBase->getMoney()->getAmount() + ($seniorityInYears * $amount);
        $this->assertEquals($expectedAmount, $totalSalary->getAmount());
        $this->assertEquals($remunerationBase->getMoney()->getCurrency(), $totalSalary->getCurrency());
    }

    /**
     * @group unit
     * @throws DepartmentAdditionToBaseInvalidValue
     */
    public function testGivenEmployeeAndRemunerationBaseWithPercentage_CalculateAdditionToBaseCorrectly(): void
    {
        $seniorityInYears = 8;
        $additionToBaseType = AdditionToBaseEnum::PERCENTAGE;
        $percentage = 5;
        $departmentAdditionToBase = new DepartmentAdditionToBase($additionToBaseType, null, $percentage);
        $employeeMock = $this->createEmployeeMock($seniorityInYears, $departmentAdditionToBase);

        $remunerationBase = new RemunerationBase(new Money(2000, new Currency('USD')));
        $payroll = new Payroll($employeeMock, $remunerationBase);

        $additionToBase = $payroll->calculateAdditionToBase();

        $expectedAmount = round($remunerationBase->getMoney()->getAmount() * ($percentage / 100));
        $this->assertEquals($expectedAmount, $additionToBase->getAmount()->getAmount());
        $this->assertEquals($remunerationBase->getMoney()->getCurrency(), $additionToBase->getAmount()->getCurrency());
        $this->assertEquals($additionToBaseType, $additionToBase->getAdditionToBaseType());
    }

    /**
     * @group unit
     * @throws DepartmentAdditionToBaseInvalidValue
     */
    public function testGivenEmployeeAndRemunerationBaseWithPercentage_GetTotalSalaryCorrectly(): void
    {
        $seniorityInYears = 8;
        $additionToBaseType = AdditionToBaseEnum::PERCENTAGE;
        $percentage = 5;
        $departmentAdditionToBase = new DepartmentAdditionToBase($additionToBaseType, null, $percentage);
        $employeeMock = $this->createEmployeeMock($seniorityInYears, $departmentAdditionToBase);

        $remunerationBase = new RemunerationBase(new Money(2000, new Currency('USD')));
        $payroll = new Payroll($employeeMock, $remunerationBase);

        $totalSalary = $payroll->getTotalSalary();

        $expectedAmount = $remunerationBase->getMoney()->getAmount() + round($remunerationBase->getMoney()->getAmount() * ($percentage / 100));
        $this->assertEquals($expectedAmount, $totalSalary->getAmount());
        $this->assertEquals($remunerationBase->getMoney()->getCurrency(), $totalSalary->getCurrency());
    }
}
