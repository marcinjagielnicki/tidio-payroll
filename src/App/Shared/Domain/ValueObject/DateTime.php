<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\DateTimeException;

/** @psalm-immutable */
final class DateTime extends \DateTimeImmutable
{
    public const FORMAT = 'Y-m-d\TH:i:s.uP';

    /**
     * @throws DateTimeException
     */
    public static function now(): self
    {
        return self::create();
    }

    /**
     * @throws DateTimeException
     */
    public static function fromString(string $dateTime): self
    {
        return self::create($dateTime);
    }

    public function toString(): string
    {
        return $this->format(self::FORMAT);
    }

    /**
     * @throws DateTimeException
     */
    private static function create(string $dateTime = ''): self
    {
        try {
            return new self($dateTime);
        } catch (\Throwable $e) {
            throw new DateTimeException(new \Exception($e->getMessage(), (int) $e->getCode(), $e));
        }
    }
}
