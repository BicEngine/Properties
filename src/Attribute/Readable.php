<?php

declare(strict_types=1);

namespace Bic\Properties\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Readable extends Property
{
    public function __construct()
    {
        parent::__construct(set: false);
    }
}
