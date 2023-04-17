<?php

declare(strict_types=1);

namespace App\Shared\Domain\Filter;

final readonly class StringFilter implements FilterInterface
{
    public function __construct(
        private string $columnName,
        private string $value
    ) {
    }

    public function getColumnName(): string
    {
        return $this->columnName;
    }

    public function getFilterValue(): string
    {
        return $this->value;
    }
}
