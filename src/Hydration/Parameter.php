<?php

namespace Recoded\ObjectHydrator\Hydration;

/**
 * @internal
 */
readonly class Parameter
{
    /**
     * Create a new Parameter instance.
     *
     * @param string $name
     * @param \Recoded\ObjectHydrator\Hydration\ParameterType|null $type
     * @param mixed $default
     * @param \Recoded\ObjectHydrator\Contracts\Mapping\DataMapper[] $attributes
     * @param \Recoded\ObjectHydrator\Contracts\Mapping\TypeMapper[] $typeMappers
     * @return void
     */
    public function __construct(
        public string $name,
        public ?ParameterType $type,
        public mixed $default,
        public array $attributes, // TODO rename to dataMappers
        public array $typeMappers,
    ) {
    }
}
