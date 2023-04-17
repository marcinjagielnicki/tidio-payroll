<?php

declare(strict_types=1);

namespace App\Employee\Domain;

use App\Department\Domain\Department;
use App\Payroll\Domain\ValueObject\RemunerationBase;
use App\Shared\Domain\ValueObject\DateTime;
use Symfony\Component\Uid\Uuid;

class Employee
{
    public function __construct(
        public Uuid $uuid,
        public string $firstName,
        public string $lastName,
        public Department $department,
        public RemunerationBase $remunerationBase,
        public DateTime $employmentStartDate,
        public DateTime $createdAt,
        public DateTime $updatedAt
    ) {
    }
}
