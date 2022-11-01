<?php

declare(strict_types=1);

namespace Bic\Properties;

use Bic\Properties\Exception\NonReadablePropertyException;
use Bic\Properties\Exception\NonWritablePropertyException;

interface ContextInterface
{
    /**
     * @param non-empty-string $name
     */
    public function isReadable(string $name): bool;

    /**
     * @param non-empty-string $name
     */
    public function isWritable(string $name): bool;

    /**
     * @param non-empty-string $name
     * @throws NonReadablePropertyException
     */
    public function get(string $name): mixed;

    /**
     * @param non-empty-string $name
     * @throws NonWritablePropertyException
     */
    public function set(string $name, mixed $value): void;
}
