<?php

declare(strict_types=1);

namespace App\Payroll\Domain\Filter;

use App\Shared\Domain\Exception\FilterNotValidException;
use App\Shared\Domain\Filter\FilterInterface;
use App\Shared\Domain\Filter\StringFilter;
use App\Shared\Domain\Filter\UuidFilter;

class PayrollFilter
{
    private const AVAILABLE_FILTERS = [
        'uuid' => UuidFilter::class,
        'firstName' => StringFilter::class,
        'lastName' => StringFilter::class,
        'departmentName' => StringFilter::class,
    ];

    private FilterInterface $filter;

    /**
     * @throws FilterNotValidException
     */
    public function __construct(FilterInterface $filter)
    {
        if (! isset(self::AVAILABLE_FILTERS[$filter->getColumnName()])) {
            throw FilterNotValidException::fromFilterName($filter->getColumnName());
        }

        if (! is_a($filter, self::AVAILABLE_FILTERS[$filter->getColumnName()])) {
            throw FilterNotValidException::fromFilterName($filter->getColumnName());
        }

        $this->filter = $filter;
    }

    /**
     * @throws FilterNotValidException
     */
    public static function createFromColumnNameAndValue(string $columnName, mixed $value): self
    {
        if (! isset(self::AVAILABLE_FILTERS[$columnName])) {
            throw FilterNotValidException::fromFilterName($columnName);
        }

        $className = self::AVAILABLE_FILTERS[$columnName];

        $filterClass = new $className($columnName, $value);
        return new self($filterClass);
    }

    public function getFilter(): FilterInterface
    {
        return $this->filter;
    }
}
