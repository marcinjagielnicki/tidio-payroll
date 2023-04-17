<?php

declare(strict_types=1);

namespace App\Department\Domain;

interface DepartmentRepository
{
    public function register(Department $department): void;
}
