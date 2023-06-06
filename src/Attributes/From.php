<?php

namespace Recoded\ObjectHydrator\Attributes;

use Attribute;
use Recoded\ObjectHydrator\Contracts\Mapping\ClassPrependableMapper;
use Recoded\ObjectHydrator\Data\ModifyKey;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class From implements ClassPrependableMapper
{
    /**
     * Create a new From instance.
     *
     * @param string $key
     * @param bool $root
     * @return void
     */
    public function __construct(
        public readonly string $key,
        public readonly bool $root = false,
    ) {
    }

    /**
     * Map data.
     *
     * @param mixed $value
     * @param string $name
     * @param array<array-key, mixed> $data
     * @return mixed
     */
    public function map(mixed $value, string $name, array $data): mixed
    {
        return new ModifyKey($this->key, $this->root);
    }
}
