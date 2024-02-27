<?php

namespace Recoded\ObjectHydrator\Dumping;

use Recoded\ObjectHydrator\Contracts\Mapping\DataMapper;
use Recoded\ObjectHydrator\Contracts\Mapping\TypeMapper;

/**
 * @phpstan-type DumpedAttributeState array{
 *     value: string,
 * }
 */
final readonly class DumpedAttribute
{
    private string $value;

    /**
     * Create a new DumpedAttribute instance.
     *
     * @param \Recoded\ObjectHydrator\Contracts\Mapping\DataMapper|\Recoded\ObjectHydrator\Contracts\Mapping\TypeMapper $attribute
     * @return void
     */
    public function __construct(DataMapper|TypeMapper $attribute)
    {
        $this->value = serialize($attribute);
    }

    /**
     * Re-instantiate this class based on state data.
     *
     * @param array<string, mixed> $data
     * @phpstan-param DumpedAttributeState $data
     * @return \Recoded\ObjectHydrator\Contracts\Mapping\DataMapper|\Recoded\ObjectHydrator\Contracts\Mapping\TypeMapper
     */
    public static function __set_state(array $data): DataMapper|TypeMapper
    {
        /** @var \Recoded\ObjectHydrator\Contracts\Mapping\DataMapper|\Recoded\ObjectHydrator\Contracts\Mapping\TypeMapper */
        return unserialize($data['value']);
    }
}
