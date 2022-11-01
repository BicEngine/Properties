<?php

declare(strict_types=1);

namespace Bic\Properties\Attribute;

use Bic\Properties\Exception\PropertyException;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Property
{
    /**
     * @param non-empty-string|false|null $get
     * @param non-empty-string|false|null $set
     *
     * @throws PropertyException
     */
    public function __construct(
        public readonly string|false|null $get = null,
        public readonly string|false|null $set = null,
    ) {
        if ($this->get === false && $this->set === false) {
            throw PropertyException::fromInvalidDefinition('The property must be either readable or writable');
        }
    }
}
