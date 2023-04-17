<?php

declare(strict_types=1);

namespace App\Payroll\Domain;

use App\Payroll\Domain\ValueObject\DepartmentAdditionToBase;
use App\Shared\Domain\AggregateRoot;
use Symfony\Component\Uid\Uuid;

class Department extends AggregateRoot
{
    public function __construct(
        private readonly Uuid $uuid,
        private readonly string $name,
        private readonly DepartmentAdditionToBase $additionToBase
    ) {
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAdditionToBase(): DepartmentAdditionToBase
    {
        return $this->additionToBase;
    }
}
