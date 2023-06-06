<?php

namespace Recoded\ObjectHydrator\Hydration;

final readonly class Parameter
{
    /**
     * Create a new Parameter instance.
     *
     * @param string $name
     * @param \Recoded\ObjectHydrator\Hydration\ParameterType|null $type
     * @param mixed $default
     * @param \Recoded\ObjectHydrator\Contracts\Mapping\DataMapper[] $attributes
     * @return void
     */
    public function __construct(
        public string $name,
        public ?ParameterType $type,
        public mixed $default,
        public array $attributes,
    ) {
    }
}
