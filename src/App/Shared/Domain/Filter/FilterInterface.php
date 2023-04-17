<?php

declare(strict_types=1);

namespace App\Shared\Domain\Filter;

interface FilterInterface
{
    public function getColumnName(): string;

    public function getFilterValue(): mixed;
}
