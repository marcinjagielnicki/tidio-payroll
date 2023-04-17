<?php

declare(strict_types=1);

namespace App\Department\Domain;

use App\Employee\Domain\Employee;
use App\Payroll\Domain\ValueObject\DepartmentAdditionToBase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;

class Department
{
    /**
     * @var Collection<int, Employee>
     */
    private Collection $employees;

    public function __construct(
        public Uuid $uuid,
        public string $name,
        public DepartmentAdditionToBase $departmentAdditionToBase
    ) {
        $this->employees = new ArrayCollection();
    }

    /**
     * @return Collection<int, Employee>
     */
    public function getEmployees(): Collection
    {
        return $this->employees;
    }

    /**
     * @param Collection<int, Employee> $employees
     */
    public function setEmployees(Collection $employees): void
    {
        $this->employees = $employees;
    }
}
