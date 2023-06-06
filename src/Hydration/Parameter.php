<?php

namespace Recoded\ObjectHydrator\Hydration;

final readonly class Parameter
{
    /**
     * Create a new Parameter instance.
     *
     * @param string $name
     * @param mixed $default
     * @param \Recoded\ObjectHydrator\Contracts\Mapping\DataMapper[] $attributes
     * @return void
     */
    public function __construct(
        public string $name,
        public mixed $default,
        public array $attributes,
    ) {
    }
}
