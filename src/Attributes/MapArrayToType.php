<?php

namespace Recoded\ObjectHydrator\Attributes;

use Attribute;
use Recoded\ObjectHydrator\Contracts\Hydrator;
use Recoded\ObjectHydrator\Contracts\Mapping\DataMapper;
use Recoded\ObjectHydrator\Contracts\Mapping\HydratorAware;
use UnexpectedValueException;

#[Attribute(Attribute::TARGET_PARAMETER)]
class MapArrayToType implements DataMapper, HydratorAware
{
    protected Hydrator $hydrator;

    /**
     * Create a new MapToType instance.
     *
     * @param class-string $type
     * @param class-string<\Recoded\ObjectHydrator\Contracts\Mapping\HydratorResolver>|null $hydratorResolver
     * @return void
     */
    public function __construct(
        protected readonly string $type,
        protected readonly ?string $hydratorResolver = null,
    ) {
    }

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
        if (!is_array($value)) {
            return $value;
        }

        $hydrator = $this->hydratorResolver !== null
            ? $this->hydratorResolver::resolve($data)
            : $this->hydrator;

        return array_map(function (mixed $item) use ($hydrator) {
            if (!is_array($item) && !is_object($item)) {
                throw new UnexpectedValueException('Expected array or object in array, got: ' . get_debug_type($item));
            }

            return $hydrator->hydrate($item, $this->type);
        }, $value);
    }

    /**
     * Set the hydrator.
     *
     * @param \Recoded\ObjectHydrator\Contracts\Hydrator $hydrator
     * @return void
     */
    public function setHydrator(Hydrator $hydrator): void
    {
        $this->hydrator = $hydrator;
    }
}
