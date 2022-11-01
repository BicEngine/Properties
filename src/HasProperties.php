<?php

declare(strict_types=1);

namespace Bic\Properties;

use Bic\Properties\Exception\NonReadablePropertyException;
use Bic\Properties\Exception\NonWritablePropertyException;
use Bic\Properties\Exception\PropertyException;

trait HasProperties
{
    /**
     * @var ContextInterface|null
     */
    private ?ContextInterface $__properties = null;

    /**
     * @param non-empty-string $name
     *
     * @return bool
     * @throws PropertyException
     * @throws \ReflectionException
     */
    public function __isset(string $name): bool
    {
        $this->__properties ??= Reader::create($this);

        return $this->__properties->isReadable($name)
            || $this->__properties->isWritable($name);
    }

    /**
     * @param non-empty-string $name
     *
     * @throws PropertyException
     * @throws \ReflectionException
     */
    public function __unset(string $name): void
    {
        $this->__properties ??= Reader::create($this);

        if ($this->__properties->isWritable($name)) {
            unset($this->$name);
        }
    }

    /**
     * @param non-empty-string $name
     *
     * @return mixed
     * @throws NonReadablePropertyException
     * @throws PropertyException
     * @throws \ReflectionException
     */
    public function __get(string $name): mixed
    {
        return ($this->__properties ??= Reader::create($this))->get($name);
    }

    /**
     * @param non-empty-string $name
     * @param mixed $value
     *
     * @throws NonWritablePropertyException
     * @throws PropertyException
     * @throws \ReflectionException
     */
    public function __set(string $name, mixed $value): void
    {
        ($this->__properties ??= Reader::create($this))->set($name, $value);
    }
}
