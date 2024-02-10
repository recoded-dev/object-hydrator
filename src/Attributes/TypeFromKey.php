<?php

namespace Recoded\ObjectHydrator\Attributes;

use Attribute;
use Recoded\ObjectHydrator\Contracts\Mapping\TypeMapper;
use Recoded\ObjectHydrator\Hydration\PlanExecutor;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::IS_REPEATABLE)]
class TypeFromKey implements TypeMapper
{
    /**
     * Create a new From instance.
     *
     * @param string $key
     * @param array<array-key, class-string> $types
     * @return void
     */
    public function __construct(
        public readonly string $key,
        public readonly array $types,
    ) {
    }

    /**
     * Map type of parameter.
     *
     * @param string|null $type
     * @param array<array-key, mixed>|object $data
     * @return class-string|null
     */
    public function map(?string $type, array|object $data): ?string
    {
        $value = PlanExecutor::get($data, $this->key);

        if (!is_string($value) && !is_int($value)) {
            return null;
        }

        return $this->types[$value] ?? null;
    }
}
