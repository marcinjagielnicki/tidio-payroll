<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exception;

class FilterNotValidException extends \Exception
{
    public static function fromFilterName(string $name): self
    {
        return new self(sprintf('Filter for %s is not valid', $name));
    }
}
