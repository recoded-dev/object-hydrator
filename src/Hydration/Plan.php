<?php

namespace Recoded\ObjectHydrator\Hydration;

/**
 * @template TClass
 * @phpstan-type PlanState array{
 *     initializer: string|null,
 *     parameters: list<\Recoded\ObjectHydrator\Hydration\Parameter>,
 * }
 */
final readonly class Plan
{
    /**
     * Everytime a breaking change happens to this class or is underlying components
     * increase this version.
     */
    public const VERSION = 1;

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

    /**
     * Re-instantiate this class based on state data.
     *
     * @param array<string, mixed> $data
     * @phpstan-param PlanState $data
     * @return self<object>
     */
    public static function __set_state(array $data): self
    {
        return new self(
            initializer: $data['initializer'],
            parameters: $data['parameters'],
        );
    }
}
