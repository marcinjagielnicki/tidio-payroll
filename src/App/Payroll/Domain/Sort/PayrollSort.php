<?php

declare(strict_types=1);

namespace App\Payroll\Domain\Sort;

use Webmozart\Assert\Assert;

class PayrollSort
{
    private const AVAILABLE_SORTS = [
        'uuid',
        'firstName',
        'lastName',
        'totalSalary',
        'additionToBase',
        'department.name',
        'remunerationBase',
        'department.bonusType',
    ];

    public function __construct(
        private readonly string $sortBy,
        private readonly string $direction
    ) {
        Assert::oneOf($sortBy, self::AVAILABLE_SORTS);
        Assert::oneOf($this->direction, ['ASC', 'DESC']);
    }

    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }
}
