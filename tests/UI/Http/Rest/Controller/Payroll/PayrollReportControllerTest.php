<?php

declare(strict_types=1);

namespace Tests\UI\Http\Rest\Controller\Payroll;

use App\Department\Domain\Department;
use App\Department\Domain\DepartmentRepository;
use App\Employee\Domain\Employee;
use App\Employee\Domain\EmployeeRepository;
use App\Payroll\Domain\Enum\AdditionToBaseEnum;
use App\Payroll\Domain\ValueObject\DepartmentAdditionToBase;
use App\Payroll\Domain\ValueObject\RemunerationBase;
use App\Shared\Domain\ValueObject\Currency;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\Money;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class PayrollReportControllerTest extends WebTestCase
{
    private function createDummyDepartment(): Department
    {
        /** @var DepartmentRepository $departmentRepo */
        $departmentRepo = $this->getContainer()->get(DepartmentRepository::class);
        $department = new Department(
            Uuid::v7(),
            'DUMMY_IT',
            new DepartmentAdditionToBase(AdditionToBaseEnum::YEARS_OF_WORKING, 150 * 100)
        );

        $departmentRepo->register($department);

        return $department;
    }

    private function createDummyEmployees(string $name = 'John', string $lastName = 'Snow', int $remunerationAmount = 120000): void
    {
        $department = $this->createDummyDepartment();

        /** @var EmployeeRepository $employeeRepo */
        $employeeRepo = $this->getContainer()->get(EmployeeRepository::class);

        $employee = new Employee(
            Uuid::v7(),
            $name,
            $lastName,
            $department,
            new RemunerationBase(new Money($remunerationAmount, new Currency('USD'))),
            DateTime::fromString('2019-01-06'),
            new DateTime(),
            new DateTime()
        );

        $employeeRepo->register($employee);
    }

    public function testPayrollReportController(): void
    {
        $client = static::createClient();

        $this->createDummyEmployees();

        $client->request(Request::METHOD_GET, '/api/payroll/report');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();

        $this->assertNotEmpty($content);

        $response = json_decode($content, true);
        $this->assertCount(1, $response);

        $firstObject = $response[0];
        $this->assertEquals('John', $firstObject['name']);
        $this->assertEquals('Snow', $firstObject['lastName']);
        $this->assertEquals(600, $firstObject['additionToBase']);
        $this->assertEquals(AdditionToBaseEnum::YEARS_OF_WORKING->value, $firstObject['bonusType']);
        $this->assertEquals(1800, $firstObject['salaryWithBonus']);
    }

    public function testPayrollReportController_WithFilterAndSort(): void
    {
        $client = static::createClient();

        $this->createDummyEmployees();
        $this->createDummyEmployees('John', 'Snow2');
        $this->createDummyEmployees('Joshua', 'Snow2');

        $client->request(Request::METHOD_GET, '/api/payroll/report', [
            'filterBy' => 'firstName',
            'filter' => 'John',
            'sortBy' => 'lastName',
            'sortByDirection' => 'DESC',
        ]);

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $content = $client->getResponse()->getContent();

        $this->assertNotEmpty($content);

        $response = json_decode($content, true);
        $this->assertCount(2, $response);

        $firstObject = $response[0];
        $secondObject = $response[1];
        $this->assertEquals('Snow2', $firstObject['lastName']);
        $this->assertEquals('Snow', $secondObject['lastName']);
    }

    public function testPayrollReportController_WithInvalidFilter(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/api/payroll/report', [
            'filterBy' => 'invalidFilter',
            'filter' => 'John',
        ]);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    public function testPayrollReportController_WithInvalidSort(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/api/payroll/report', [
            'sortBy' => 'invalidSort',
            'sortByDirection' => 'DESC',
        ]);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }
}
