<?php

declare(strict_types=1);

namespace App\Shared\Domain\Filter;

use App\Shared\Domain\Exception\FilterNotValidException;
use Symfony\Component\Uid\Uuid;

final class UuidFilter implements FilterInterface
{
    private Uuid $uuid;

    /**
     * @throws FilterNotValidException
     */
    public function __construct(
        private readonly string $columnName,
        string $uuid
    ) {
        if (! Uuid::isValid($uuid)) {
            throw FilterNotValidException::fromFilterName($this->columnName);
        }

        $this->uuid = Uuid::fromString($uuid);
    }

    public function getColumnName(): string
    {
        return $this->columnName;
    }

    public function getFilterValue(): Uuid
    {
        return $this->uuid;
    }
}
