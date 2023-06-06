<?php

namespace Recoded\ObjectHydrator\Hydration;

/**
 * @phpstan-type ParameterTypeState array{
 *     name: class-string,
 *     nullable: bool,
 *     resolver: class-string<\Recoded\ObjectHydrator\Contracts\Mapping\HydratorResolver>|null,
 * }
 */
final readonly class ParameterType
{
    /**
     * Create a new ParameterType instance.
     *
     * @param class-string $name
     * @param bool $nullable
     * @param class-string<\Recoded\ObjectHydrator\Contracts\Mapping\HydratorResolver>|null $resolver
     * @return void
     */
    public function __construct(
        public string $name,
        public bool $nullable,
        public ?string $resolver,
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
            name: $data['name'],
            nullable: $data['nullable'],
            resolver: $data['resolver'],
        );
    }
}
