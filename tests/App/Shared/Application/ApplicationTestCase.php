<?php

declare(strict_types=1);

namespace Tests\App\Shared\Application;

use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Application\Command\CommandInterface;
use App\Shared\Application\Query\QueryBusInterface;
use App\Shared\Application\Query\QueryInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;

abstract class ApplicationTestCase extends KernelTestCase
{
    private ?CommandBusInterface $commandBus;

    private ?QueryBusInterface $queryBus;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        self::bootKernel();

        /** @var CommandBusInterface $commandBus */
        $commandBus = $this->service(CommandBusInterface::class);
        $this->commandBus = $commandBus;

        /** @var QueryBusInterface $queryBus */
        $queryBus = $this->service(QueryBusInterface::class);
        $this->queryBus = $queryBus;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->commandBus = null;
        $this->queryBus = null;
    }

    protected function ask(QueryInterface $query): mixed
    {
        if ($this->queryBus) {
            return $this->queryBus->ask($query);
        }
        return null;
    }

    /**
     * @throws Throwable
     */
    protected function handle(CommandInterface $command): void
    {
        if ($this->commandBus) {
            $this->commandBus->handle($command);
        }
    }

    protected function service(string $serviceId): ?object
    {
        return $this->getContainer()->get($serviceId);
    }

    /**
     * @throws Exception
     */
    protected function fireTerminateEvent(): void
    {
        /** @var EventDispatcher $dispatcher */
        $dispatcher = $this->service(EventDispatcherInterface::class);

        $dispatcher->dispatch(
            new TerminateEvent(
                static::$kernel,
                Request::create('/'),
                new Response()
            ),
            KernelEvents::TERMINATE
        );
    }
}
