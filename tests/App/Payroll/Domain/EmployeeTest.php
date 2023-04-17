<?php

declare(strict_types=1);

namespace Tests\App\Payroll\Domain;

use App\Payroll\Domain\Department;
use App\Payroll\Domain\Employee;
use App\Shared\Domain\ValueObject\DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class EmployeeTest extends TestCase
{
    /**
     * @group unit
     */
    public function testGivenValidData_ReturnsCorrectValues(): void
    {
        $uuid = Uuid::v4();
        $firstName = 'John';
        $lastName = 'Doe';
        $department = $this->getMockBuilder(Department::class)
            ->disableOriginalConstructor()
            ->getMock();

        $employmentDate = new DateTime('2018-06-01');

        $employee = new Employee(
            $uuid,
            $firstName,
            $lastName,
            $department,
            $employmentDate
        );

        $this->assertEquals($uuid, $employee->getUuid());
        $this->assertEquals($firstName, $employee->getFirstName());
        $this->assertEquals($lastName, $employee->getLastName());
        $this->assertEquals($department, $employee->getDepartment());
        $this->assertEquals($employmentDate, $employee->getEmploymentDate());
    }

    /**
     * @group unit
     */
    public function testGivenEmploymentDate_CalculatesSeniorityInYears(): void
    {
        $uuid = Uuid::v4();
        $firstName = 'John';
        $lastName = 'Doe';
        $department = $this->getMockBuilder(Department::class)
            ->disableOriginalConstructor()
            ->getMock();

        $employmentDate = new DateTime('2018-06-01');

        $employee = new Employee(
            $uuid,
            $firstName,
            $lastName,
            $department,
            $employmentDate
        );

        $now = new DateTime();
        $expectedSeniority = $employmentDate->diff($now)->y;

        $this->assertEquals($expectedSeniority, $employee->getSeniorityInYears());
    }
}
