<?php

declare(strict_types=1);

namespace App\Employee\Infrastructure\Persistence\Doctrine\Fixture;

use App\Department\Domain\Department;
use App\Department\Infrastructure\Persistence\Doctrine\Fixture\DepartmentFixtures;
use App\Employee\Domain\Employee;
use App\Employee\Domain\EmployeeRepository;
use App\Payroll\Domain\ValueObject\RemunerationBase;
use App\Shared\Domain\ValueObject\Currency;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\Money;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

class EmployeeFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly EmployeeRepository $employeeRepository
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        /** @var Department $itDepartment */
        $itDepartment = $this->getReference(DepartmentFixtures::IT_DEPARTMENT_REF);

        $employee = new Employee(
            Uuid::v7(),
            'John',
            'Snow',
            $itDepartment,
            new RemunerationBase(new Money(1200 * 100, new Currency('USD'))),
            DateTime::fromString('2019-01-06'),
            new DateTime(),
            new DateTime()
        );

        $this->employeeRepository->register($employee);

        $this->createRandomEmployees();
    }

    public function createRandomEmployees(): void
    {
        $departments = [
            DepartmentFixtures::IT_DEPARTMENT_REF,
            DepartmentFixtures::HR_DEPARTMENT_REF,
            DepartmentFixtures::CS_DEPARTMENT_REF,
        ];

        for ($i = 0; $i <= 20; $i++) {
            $randomDepartmentRefName = $departments[rand(0, count($departments) - 1)];

            /** @var Department $randomDepartment */
            $randomDepartment = $this->getReference($randomDepartmentRefName);

            $employee = new Employee(
                Uuid::v7(),
                'John_' . $i,
                'Snow_' . $i,
                $randomDepartment,
                new RemunerationBase(new Money(rand(1000, 2000) * 100, new Currency('USD'))),
                DateTime::fromString(rand(2015, 2023) . '-01-06'),
                new DateTime(),
                new DateTime()
            );

            $this->employeeRepository->register($employee);
        }
    }

    public function getDependencies(): array
    {
        return [DepartmentFixtures::class];
    }
}
