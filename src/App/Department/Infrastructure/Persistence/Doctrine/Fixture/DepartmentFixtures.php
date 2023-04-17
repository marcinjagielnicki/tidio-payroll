<?php

declare(strict_types=1);

namespace App\Department\Infrastructure\Persistence\Doctrine\Fixture;

use App\Department\Domain\Department;
use App\Department\Domain\DepartmentRepository;
use App\Payroll\Domain\Enum\AdditionToBaseEnum;
use App\Payroll\Domain\ValueObject\DepartmentAdditionToBase;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

final class DepartmentFixtures extends Fixture
{
    final public const IT_DEPARTMENT_REF = 'it_dep_ref';

    final public const HR_DEPARTMENT_REF = 'hr_dep_ref';

    final public const CS_DEPARTMENT_REF = 'cs_dep_ref';

    public function __construct(
        private DepartmentRepository $departmentRepository
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $this->createITDepartment();
        $this->createHRDepartment();
        $this->createCSDepartment();
    }

    private function createITDepartment(): void
    {
        $department = new Department(
            Uuid::v7(),
            'IT',
            new DepartmentAdditionToBase(AdditionToBaseEnum::YEARS_OF_WORKING, 150 * 100)
        );
        $this->addReference(self::IT_DEPARTMENT_REF, $department);
        $this->departmentRepository->register($department);
    }

    private function createHRDepartment(): void
    {
        $department = new Department(
            Uuid::v7(),
            'HR',
            new DepartmentAdditionToBase(AdditionToBaseEnum::PERCENTAGE, null, 20)
        );
        $this->addReference(self::HR_DEPARTMENT_REF, $department);
        $this->departmentRepository->register($department);
    }

    private function createCSDepartment(): void
    {
        $department = new Department(
            Uuid::v7(),
            'Customer Service',
            new DepartmentAdditionToBase(AdditionToBaseEnum::PERCENTAGE, null, 10)
        );
        $this->addReference(self::CS_DEPARTMENT_REF, $department);
        $this->departmentRepository->register($department);
    }
}
