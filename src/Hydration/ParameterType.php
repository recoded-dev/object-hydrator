<?php

namespace Recoded\ObjectHydrator\Hydration;

final readonly class ParameterType
{
    /**
     * Create a new ParameterType instance.
     *
     * @param string $name
     * @param bool $nullable
     * @param string|null $resolver
     * @return void
     */
    public function __construct(
        public string $name,
        public bool $nullable,
        public ?string $resolver,
    ) {
    }
}
