<?php

namespace Recoded\ObjectHydrator\Hydration;

/**
 * @phpstan-type ParameterState array{
 *     name: string,
 *     type: \Recoded\ObjectHydrator\Hydration\ParameterType,
 *     default: mixed,
 *     attributes: \Recoded\ObjectHydrator\Contracts\Mapping\DataMapper[],
 * }
 */
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

    /**
     * Re-instantiate this class based on state data.
     *
     * @param array<string, mixed> $data
     * @phpstan-param ParameterState $data
     * @return self
     */
    public static function __set_state(array $data): self
    {
        return new self(
            name: $data['name'],
            type: $data['type'],
            default: $data['default'],
            attributes: $data['attributes'],
        );
    }
}
