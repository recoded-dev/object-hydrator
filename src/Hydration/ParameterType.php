<?php

namespace Recoded\ObjectHydrator\Hydration;

/**
 * @phpstan-type ParameterTypeState array{
 *     types: non-empty-list<class-string>,
 *     nullable: bool,
 *     resolver: class-string<\Recoded\ObjectHydrator\Contracts\Mapping\HydratorResolver>|null,
 *     composition: \Recoded\ObjectHydrator\Hydration\ParameterTypeComposition,
 * }
 */
final readonly class ParameterType
{
    /**
     * Create a new ParameterType instance.
     *
     * @param non-empty-list<class-string> $types
     * @param bool $nullable
     * @param class-string<\Recoded\ObjectHydrator\Contracts\Mapping\HydratorResolver>|null $resolver
     * @param \Recoded\ObjectHydrator\Hydration\ParameterTypeComposition $composition
     * @return void
     */
    public function __construct(
        public array $types,
        public bool $nullable,
        public ?string $resolver,
        public ParameterTypeComposition $composition,
    ) {
    }

    /**
     * Re-instantiate this class based on state data.
     *
     * @param array<string, mixed> $data
     * @phpstan-param ParameterTypeState $data
     * @return self
     */
    public static function __set_state(array $data): self
    {
        return new self(
            types: $data['types'],
            nullable: $data['nullable'],
            resolver: $data['resolver'],
            composition: $data['composition'],
        );
    }

    /**
     * Get the main type.
     *
     * @return class-string
     */
    public function main(): string
    {
        return $this->types[0];
    }
}
