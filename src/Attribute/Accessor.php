<?php

declare(strict_types=1);

namespace Bic\Properties\Attribute;

use Bic\Properties\Exception\PropertyException;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Accessor extends Property
{
    /**
     * @param non-empty-string $method
     *
     * @throws PropertyException
     */
    public function __construct(string $method)
    {
        parent::__construct($method, false);
    }
}
