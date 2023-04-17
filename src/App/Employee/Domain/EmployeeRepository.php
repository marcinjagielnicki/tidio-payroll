<?php

declare(strict_types=1);

namespace App\Employee\Domain;

interface EmployeeRepository
{
    public function register(Employee $employee): void;
}
