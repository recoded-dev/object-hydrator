<?php

namespace Recoded\ObjectHydrator\Attributes;

use Attribute;
use Recoded\ObjectHydrator\Contracts\Mapping\ClassPrependableMapper;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_CLASS)]
class EmptyStringToNull implements ClassPrependableMapper
{
    /**
     * Map data.
     *
     * @param mixed $value
     * @param string $name
     * @param array<array-key, mixed>|object $data
     * @return mixed
     */
    public function map(mixed $value, string $name, array|object $data): mixed
    {
        return $value === '' ? null : $value;
    }
}
