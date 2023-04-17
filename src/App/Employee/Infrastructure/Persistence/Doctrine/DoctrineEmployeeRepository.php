<?php

declare(strict_types=1);

namespace App\Employee\Infrastructure\Persistence\Doctrine;

use App\Employee\Domain\Employee;
use App\Employee\Domain\EmployeeRepository;
use App\Shared\Infrastructure\Persistence\ReadModel\Repository\DoctrineRepository;
use Doctrine\ORM\EntityRepository;

/**
 * @extends DoctrineRepository<Employee>
 */
final class DoctrineEmployeeRepository extends DoctrineRepository implements EmployeeRepository
{
    protected function setEntityManager(): void
    {
        /** @var EntityRepository<Employee> $objectRepository */
        $objectRepository = $this->entityManager->getRepository(Employee::class);

        $this->repository = $objectRepository;
    }
}
