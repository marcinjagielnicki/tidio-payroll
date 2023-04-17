<?php

declare(strict_types=1);

namespace App\Payroll\Infrastructure\Persistence\Doctrine;

use App\Payroll\Domain\Department;
use App\Payroll\Domain\Employee;
use App\Payroll\Domain\Enum\AdditionToBaseEnum;
use App\Payroll\Domain\Exception\DepartmentAdditionToBaseInvalidValue;
use App\Payroll\Domain\Filter\PayrollFilter;
use App\Payroll\Domain\Payroll;
use App\Payroll\Domain\PayrollRepository;
use App\Payroll\Domain\Sort\PayrollSort;
use App\Payroll\Domain\ValueObject\DepartmentAdditionToBase;
use App\Payroll\Domain\ValueObject\RemunerationBase;
use App\Shared\Domain\Exception\DateTimeException;
use App\Shared\Domain\ValueObject\Currency;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\Money;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Component\Uid\Uuid;

final readonly class DbalPayrollRepository implements PayrollRepository
{
    public function __construct(
        private Connection $connection
    ) {
    }

    protected function createSelectQueryBuilder(): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select(
            'e.uuid',
            'e.first_name',
            'e.last_name',
            'e.employment_start_date',
            'e.remuneration_base_amount',
            'e.remuneration_base_currency_code',
            'd.uuid as department_uuid',
            'd.name',
            'd.addition_to_base_type',
            'd.amount as addition_to_base_amount',
            'd.percentage as addition_to_base_percentage'
        )->from('employee', 'e')
            ->leftJoin('e', 'department', 'd', 'e.department_id = d.uuid');

        return $qb;
    }

    /**
     * @return Payroll[]
     * @throws Exception
     */
    public function listPayroll(?PayrollFilter $filter = null, ?PayrollSort $payrollSort = null): array
    {
        $qb = $this->createSelectQueryBuilder();

        if ($filter) {
            if ($filter->getFilter()->getColumnName() === 'uuid') {
                $qb->andWhere('e.uuid = :uuid');
                $qb->setParameter('uuid', $filter->getFilter()->getFilterValue(), 'uuid');
            }

            if ($filter->getFilter()->getColumnName() === 'firstName') {
                $qb->andWhere('e.first_name = :firstName');
                $qb->setParameter('firstName', $filter->getFilter()->getFilterValue());
            }

            if ($filter->getFilter()->getColumnName() === 'lastName') {
                $qb->andWhere('e.last_name = :firstName');
                $qb->setParameter('lastName', $filter->getFilter()->getFilterValue());
            }

            if ($filter->getFilter()->getColumnName() === 'departmentName') {
                $qb->andWhere('d.name = :departmentName');
                $qb->setParameter('departmentName', $filter->getFilter()->getFilterValue());
            }
        }

        if ($payrollSort) {
            $sortByColumn = match ($payrollSort->getSortBy()) {
                'firstName' => 'e.first_name',
                'lastName' => 'e.last_name',
                'uuid' => 'e.uuid',
                'department.name' => 'd.name',
                'department.bonusType' => 'd.addition_to_base_type',
                'remunerationBase' => 'e.remuneration_base_amount',
                default => null
            };

            if ($sortByColumn) {
                $qb->addOrderBy($sortByColumn, $payrollSort->getDirection());
            }
        }
        $results = $qb->fetchAllAssociative();

        $results = array_map(fn (array $data) => $this->mapListData($data), $results);

        if ($payrollSort) {
            $this->sortByCalculatedField($results, $payrollSort->getSortBy(), $payrollSort->getDirection());
        }

        return $results;
    }

    /**
     * @param array<string, mixed> $data
     * @throws DepartmentAdditionToBaseInvalidValue
     * @throws DateTimeException
     */
    private function mapListData(array $data): Payroll
    {
        $additionToBase = new DepartmentAdditionToBase(AdditionToBaseEnum::from($data['addition_to_base_type']), $data['addition_to_base_amount'], $data['addition_to_base_percentage']);

        $department = new Department(
            Uuid::fromString($data['department_uuid']),
            $data['name'],
            $additionToBase
        );

        $employee = new Employee(
            Uuid::fromString($data['uuid']),
            $data['first_name'],
            $data['last_name'],
            $department,
            DateTime::fromString($data['employment_start_date'])
        );

        $remunerationBase = new RemunerationBase(new Money($data['remuneration_base_amount'], new Currency($data['remuneration_base_currency_code'])));

        return new Payroll($employee, $remunerationBase);
    }

    /**
     * @param Payroll[] $data
     */
    private function sortByCalculatedField(array &$data, string $field, string $direction): void
    {
        $sortFunction = function (int $valueA, int $valueB, string $direction): int {
            if ($direction === 'ASC') {
                return $valueA > $valueB ? 1 : 0;
            } else {
                return $valueB > $valueA ? 1 : 0;
            }
        };

        match ($field) {
            'totalSalary' => usort($data, fn (Payroll $a, Payroll $b) => $sortFunction($a->getTotalSalary()->getAmount(), $b->getTotalSalary()->getAmount(), $direction)),
            'additionToBase' => usort($data, fn (Payroll $a, Payroll $b) => $sortFunction($a->calculateAdditionToBase()->getAmount()->getAmount(), $b->calculateAdditionToBase()->getAmount()->getAmount(), $direction)),
            default => false
        };
    }
}
