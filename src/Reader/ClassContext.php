<?php

declare(strict_types=1);

namespace Bic\Properties\Reader;

use Bic\Properties\Attribute\Property;
use Bic\Properties\Exception\NonWritablePropertyException;
use Bic\Properties\Exception\PropertyException;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Bic\Properties
 */
final class ClassContext
{
    /**
     * @var array<non-empty-string, callable():mixed>
     */
    private array $getters = [];

    /**
     * @var array<non-empty-string, callable(mixed):void>
     */
    private array $setters = [];

    /**
     * @param class-string $class
     *
     * @throws PropertyException
     * @throws \ReflectionException
     */
    public function __construct(
        private readonly string $class,
    ) {
        $reflection = new \ReflectionClass($this->class);

        foreach ($reflection->getProperties() as $property) {
            $attributes = $property->getAttributes(Property::class, \ReflectionAttribute::IS_INSTANCEOF);

            if ($attributes === []) {
                continue;
            }

            if ($property->isStatic()) {
                $message = 'Unable to declare a #[Property] attribute on a static property %s::$%s';
                throw PropertyException::fromInvalidDefinition(\sprintf($message, $this->class, $property->name));
            }

            foreach ($attributes as $attribute) {
                $getter = $this->createGetter($property, $attribute->newInstance());
                $setter = $this->createSetter($property, $attribute->newInstance());

                if ($getter !== null) {
                    if (isset($this->getters[$property->name])) {
                        $message = '%s::$%s property reading logic has already been defined';
                        throw PropertyException::fromInvalidDefinition(
                            \sprintf($message, $this->class, $property->name)
                        );
                    }

                    $this->getters[$property->name] = $getter;
                }

                if ($setter !== null) {
                    if (isset($this->setters[$property->name])) {
                        $message = '%s::$%s property writing logic has already been defined';
                        throw PropertyException::fromInvalidDefinition(
                            \sprintf($message, $this->class, $property->name)
                        );
                    }

                    $this->setters[$property->name] = $setter;
                }
            }
        }
    }

    /**
     * @param object $context
     *
     * @return ObjectContext
     */
    public function create(object $context): ObjectContext
    {
        return new ObjectContext($context, $this->getters, $this->setters);
    }

    /**
     * @return null|\Closure():mixed
     */
    private function createGetter(\ReflectionProperty $property, Property $attr): ?\Closure
    {
        if ($attr->get === false) {
            return null;
        }

        if ($attr->get === null) {
            $getter = $property->name;
            return (fn (): mixed => $this->{$getter});
        }

        $getter = $attr->get;
        return (fn (): mixed => $this->{$getter}());
    }

    /**
     * @param \ReflectionProperty $property
     * @param Property $attr
     *
     * @return null|\Closure(mixed):void
     */
    private function createSetter(\ReflectionProperty $property, Property $attr): ?\Closure
    {
        if ($attr->set === false) {
            return null;
        }

        if ($attr->set === null) {
            $setter = $property->name;
            return (fn (mixed $value): mixed => $this->{$setter} = $value);
        }

        $setter = $attr->set;
        return (fn (mixed $value): mixed => $this->{$setter}($value));
    }
}
