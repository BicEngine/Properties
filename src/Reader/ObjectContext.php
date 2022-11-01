<?php

declare(strict_types=1);

namespace Bic\Properties\Reader;

use Bic\Properties\ContextInterface;
use Bic\Properties\Exception\NonReadablePropertyException;
use Bic\Properties\Exception\NonWritablePropertyException;

final class ObjectContext implements ContextInterface
{
    /**
     * @var class-string
     */
    private readonly string $class;

    /**
     * @param array<non-empty-string, \Closure():mixed> $getters
     * @param array<non-empty-string, \Closure(mixed):void> $setters
     */
    public function __construct(
        private readonly object $object,
        private readonly array $getters,
        private readonly array $setters,
    ) {
        $this->class = $this->object::class;
    }

    /**
     * {@inheritDoc}
     */
    public function isReadable(string $name): bool
    {
        return isset($this->getters[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function isWritable(string $name): bool
    {
        return isset($this->setters[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $name): mixed
    {
        if (!isset($this->getters[$name])) {
            throw NonReadablePropertyException::create($this->class, $name);
        }

        return ($this->getters[$name])->call($this->object);
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $name, mixed $value): void
    {
        if (!isset($this->setters[$name])) {
            throw NonWritablePropertyException::create($this->class, $name);
        }

        ($this->setters[$name])->call($this->object, $value);
    }

    /**
     * @return array{class:class-string, getters:array<non-empty-string>
     */
    public function __debugInfo(): array
    {
        return [
            '__get' => \array_keys($this->getters),
            '__set' => \array_keys($this->setters),
        ];
    }
}
