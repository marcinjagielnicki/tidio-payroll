<?php

declare(strict_types=1);

namespace UI\Http\Rest\EventSubscriber;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;

class ExceptionSubscriber
{
    private string $environment;

    /**
     * @var array<class-string, int>
     */
    private array $exceptionToStatus;

    /**
     * @param array<class-string, int> $exceptionToStatus
     */
    public function __construct(string $environment, array $exceptionToStatus = [])
    {
        $this->environment = $environment;
        $this->exceptionToStatus = $exceptionToStatus;
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $response = new JsonResponse();
        $response->headers->set('Content-Type', 'application/vnd.api+json');
        $response->setStatusCode($this->determineStatusCode($exception));
        $response->setData($this->getErrorMessage($exception, $response));

        $event->setResponse($response);
    }

    /**
     * @return array<string, mixed>
     */
    private function getErrorMessage(Throwable $exception, Response $response): array
    {
        $error = [
            'error' => [
                'title' => \str_replace('\\', '.', \get_class($exception)),
                'detail' => $this->getExceptionMessage($exception),
                'code' => $exception->getCode(),
            ],
        ];

        if ($this->environment === 'dev' || $this->environment === 'test') {
            $error = \array_merge(
                $error,
                [
                    'meta' => [
                        'file' => $exception->getFile(),
                        'line' => $exception->getLine(),
                        'message' => $exception->getMessage(),
                        'trace' => $exception->getTrace(),
                        'traceString' => $exception->getTraceAsString(),
                    ],
                ]
            );
        }

        return $error;
    }

    private function getExceptionMessage(Throwable $exception): string
    {
        return $exception->getMessage();
    }

    private function determineStatusCode(Throwable $exception): int
    {
        $exceptionClass = \get_class($exception);

        foreach ($this->exceptionToStatus as $class => $status) {
            if (\is_a($exceptionClass, $class, true)) {
                return $status;
            }
        }

        // Process HttpExceptionInterface after `exceptionToStatus` to allow overrides from config
        if ($exception instanceof HttpExceptionInterface) {
            return $exception->getStatusCode();
        }

        // Default status code is always 500
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }
}
