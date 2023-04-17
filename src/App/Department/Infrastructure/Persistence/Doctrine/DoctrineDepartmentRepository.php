<?php

declare(strict_types=1);

namespace App\Department\Infrastructure\Persistence\Doctrine;

use App\Department\Domain\Department;
use App\Department\Domain\DepartmentRepository;
use App\Shared\Infrastructure\Persistence\ReadModel\Repository\DoctrineRepository;
use Doctrine\ORM\EntityRepository;

/**
 * @extends DoctrineRepository<Department>
 */
final class DoctrineDepartmentRepository extends DoctrineRepository implements DepartmentRepository
{
    protected function setEntityManager(): void
    {
        /** @var EntityRepository<Department> $objectRepository */
        $objectRepository = $this->entityManager->getRepository(Department::class);

        $this->repository = $objectRepository;
    }
}
