<?php

declare(strict_types=1);

namespace App\Payroll\Domain;

use App\Shared\Domain\AggregateRoot;
use App\Shared\Domain\ValueObject\DateTime;
use Symfony\Component\Uid\Uuid;

class Employee extends AggregateRoot
{
    public function __construct(
        private readonly Uuid $uuid,
        private string $firstName,
        private string $lastName,
        private Department $department,
        private DateTime $employmentDate
    ) {
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function getDepartment(): Department
    {
        return $this->department;
    }

    public function getEmploymentDate(): DateTime
    {
        return $this->employmentDate;
    }

    public function getSeniorityInYears(): int
    {
        $now = new DateTime();
        $diff = $this->getEmploymentDate()->diff($now);

        return $diff->y;
    }
}
