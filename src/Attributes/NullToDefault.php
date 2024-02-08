<?php

namespace Recoded\ObjectHydrator\Attributes;

use Attribute;
use Recoded\ObjectHydrator\Contracts\Mapping\ClassPrependableMapper;
use Recoded\ObjectHydrator\Contracts\Mapping\ParameterAware;
use Recoded\ObjectHydrator\Hydration\Parameter;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_CLASS)]
class NullToDefault implements ClassPrependableMapper, ParameterAware
{
    protected Parameter $parameter;

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
        return $value ?? $this->parameter->default;
    }

    /**
     * Set the current parameter.
     *
     * @param \Recoded\ObjectHydrator\Hydration\Parameter $parameter
     * @return void
     */
    public function setParameter(Parameter $parameter): void
    {
        $this->parameter = $parameter;
    }
}
