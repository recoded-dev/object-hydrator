<?php

namespace Recoded\ObjectHydrator\Hydration;

/**
 * @template TClass
 */
final readonly class Plan
{
    /**
     * Create a new Plan instance.
     *
     * @param string|null $initializer
     * @param list<\Recoded\ObjectHydrator\Hydration\Parameter> $parameters
     * @return void
     */
    public function __construct(
        public ?string $initializer,
        public array $parameters,
    ) {
    }
}
