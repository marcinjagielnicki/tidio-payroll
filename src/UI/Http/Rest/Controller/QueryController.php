<?php

declare(strict_types=1);

namespace UI\Http\Rest\Controller;

use App\Shared\Application\Query\QueryBusInterface;
use App\Shared\Application\Query\QueryInterface;
use Throwable;

abstract class QueryController
{
    public function __construct(
        private readonly QueryBusInterface $queryBus
    ) {
    }

    /**
     * @throws Throwable
     */
    protected function ask(QueryInterface $query): mixed
    {
        return $this->queryBus->ask($query);
    }
}
