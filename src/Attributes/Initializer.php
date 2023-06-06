<?php

namespace Recoded\ObjectHydrator\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class Initializer
{
    public function __construct(public string $method)
    {
    }
}
