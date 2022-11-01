<?php

declare(strict_types=1);

namespace Bic\Properties\Exception;

class NonReadablePropertyException extends PropertyException
{
    public static function create(string $class, string $property): static
    {
        return static::fromNonReadable($class, $property);
    }
}
