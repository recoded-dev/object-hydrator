<?php

namespace Recoded\ObjectHydrator\Attributes;

use Attribute;
use Recoded\ObjectHydrator\Contracts\Mapping\ClassPrependableMapper;
use Recoded\ObjectHydrator\Data\ModifyKey;
use RuntimeException;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_CLASS)]
class SnakeCase implements ClassPrependableMapper
{
    /**
     * The cache of snake-cased words.
     *
     * @var array<string, string>
     */
    private static array $snakeCache = [];

    /**
     * Create a new SnakeCase instance.
     *
     * @param bool $enabled
     * @return void
     */
    public function __construct(
        public readonly bool $enabled = true,
    ) {
    }

    /**
     * Apply snake_casing to a string.
     *
     * @param string $value
     * @return string
     */
    final public static function apply(string $value): string
    {
        if (isset(self::$snakeCache[$value])) {
            return self::$snakeCache[$value];
        }

        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));

            if ($value === null) {
                throw new RuntimeException('preg_replace failed');
            }

            $value = preg_replace('/(.)(?=[A-Z])/u', '$1_', $value);

            if ($value === null) {
                throw new RuntimeException('preg_replace failed');
            }

            $value = mb_strtolower($value, 'UTF-8');
        }

        return self::$snakeCache[$value] = $value;
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
        return new ModifyKey(self::apply($name));
    }
}
