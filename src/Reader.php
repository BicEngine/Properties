<?php

declare(strict_types=1);

namespace Bic\Properties;

use Bic\Properties\Exception\PropertyException;
use Bic\Properties\Reader\ObjectContext;
use Bic\Properties\Reader\ClassContext;

final class Reader
{
    /**
     * @var array<class-string, ClassContext>
     */
    private static array $contexts = [];

    /**
     * @param object $context
     *
     * @return ObjectContext
     * @throws PropertyException
     * @throws \ReflectionException
     */
    public static function create(object $context): ObjectContext
    {
        $reader = (self::$contexts[$context::class] ??= new ClassContext($context::class));

        return $reader->create($context);
    }
}
