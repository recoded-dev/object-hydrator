<?php

namespace Recoded\ObjectHydrator\Attributes;

use ArrayAccess;
use Attribute;
use Recoded\ObjectHydrator\Contracts\Mapping\DataMapper;
use Recoded\ObjectHydrator\Contracts\Mapping\OrderAware;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::IS_REPEATABLE)]
class From implements DataMapper, OrderAware
{
    protected int $order;

    /**
     * Create a new From instance.
     *
     * @param string $path
     * @param bool $root
     * @return void
     */
    public function __construct(
        public readonly string $path,
        public readonly bool $root = false,
    ) {
    }

    /**
     * Map data.
     *
     * @param mixed $value
     * @param array<array-key, mixed> $data
     * @return mixed
     */
    public function map(mixed $value, array $data): mixed
    {
        $value = $this->order === 1 || $this->root ? $data : $value;
        $parts = preg_split('/(?<!\\\)\./', $this->path);

        if ($parts === false) {
            return $value;
        }

        return array_reduce($parts, function (mixed $carry, string $part) {
            $part = str_replace('\.', '.', $part);

            return $this->get($carry, $part);
        }, $value);
    }

    /**
     * Indicate what order this is called as.
     *
     * @param int $order
     * @return void
     */
    public function setOrder(int $order): void
    {
        $this->order = $order;
    }

    /**
     * Get the key from the value, arrays or objects.
     *
     * @param mixed $value
     * @param string $key
     * @return mixed
     */
    protected function get(mixed $value, string $key): mixed
    {
        if (is_array($value) || $value instanceof ArrayAccess) {
            return $value[$key] ?? null;
        }

        if (is_object($value)) {
            return $value->{$key} ?? null;
        }

        return null;
    }
}
