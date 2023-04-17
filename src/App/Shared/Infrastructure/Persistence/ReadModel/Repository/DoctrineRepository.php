<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\ReadModel\Repository;

use App\Shared\Infrastructure\Persistence\ReadModel\Exception\NotFoundException;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * @template T of object
 */
abstract class DoctrineRepository
{
    /**
     * @var EntityRepository<T>
     */
    protected EntityRepository $repository;

    public function __construct(
        protected EntityManagerInterface $entityManager
    ) {
        $this->setEntityManager();
    }

    public function register(object $model): void
    {
        $this->entityManager->persist($model);
        $this->apply();
    }

    public function apply(): void
    {
        $this->entityManager->flush();
    }

    abstract protected function setEntityManager(): void;

    /**
     * @throws NotFoundException
     * @throws NonUniqueResultException
     */
    protected function oneOrException(QueryBuilder $queryBuilder, mixed $hydration = AbstractQuery::HYDRATE_OBJECT): mixed
    {
        $model = $queryBuilder
            ->getQuery()
            ->getOneOrNullResult($hydration)
        ;

        if ($model === null) {
            throw new NotFoundException();
        }

        return $model;
    }
}
