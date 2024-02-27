<?php

namespace Recoded\ObjectHydrator\Dumping;

use Recoded\ObjectHydrator\Contracts\Mapping\DataMapper;
use Recoded\ObjectHydrator\Contracts\Mapping\TypeMapper;
use Recoded\ObjectHydrator\Hydration\Parameter;
use Recoded\ObjectHydrator\Hydration\ParameterType;

/**
 * @phpstan-type DumpedParameterState array{
 *     name: string,
 *     type: \Recoded\ObjectHydrator\Hydration\ParameterType,
 *     default: mixed,
 *     attributes: \Recoded\ObjectHydrator\Contracts\Mapping\DataMapper[],
 *     typeMappers: \Recoded\ObjectHydrator\Contracts\Mapping\TypeMapper[],
 *     dumpedDataMappers: \Recoded\ObjectHydrator\Contracts\Mapping\DataMapper[],
 *     dumpedTypeMappers: \Recoded\ObjectHydrator\Contracts\Mapping\TypeMapper[],
 * }
 */
final readonly class DumpedParameter extends Parameter
{
    /**
     * @var \Recoded\ObjectHydrator\Dumping\DumpedAttribute[]
     */
    private array $dumpedDataMappers;

    /**
     * @var \Recoded\ObjectHydrator\Dumping\DumpedAttribute[]
     */
    private array $dumpedTypeMappers;

    /**
     * Create a new Parameter instance.
     *
     * @param string $name
     * @param \Recoded\ObjectHydrator\Hydration\ParameterType|null $type
     * @param mixed $default
     * @param \Recoded\ObjectHydrator\Contracts\Mapping\DataMapper[] $attributes
     * @param \Recoded\ObjectHydrator\Contracts\Mapping\TypeMapper[] $typeMappers
     * @return void
     */
    public function __construct(
        string $name,
        ?ParameterType $type,
        mixed $default,
        array $attributes,
        array $typeMappers,
    ) {
        parent::__construct(
            name: $name,
            type: $type,
            default: $default,
            attributes: [],
            typeMappers: [],
        );

        $this->dumpedDataMappers = array_map(static function (DataMapper $mapper) {
            return new DumpedAttribute(attribute: $mapper);
        }, $attributes);

        $this->dumpedTypeMappers = array_map(static function (TypeMapper $mapper) {
            return new DumpedAttribute(attribute: $mapper);
        }, $typeMappers);
    }

    /**
     * Re-instantiate this class based on state data.
     *
     * @param array<string, mixed> $data
     * @phpstan-param DumpedParameterState $data
     * @return \Recoded\ObjectHydrator\Hydration\Parameter
     */
    public static function __set_state(array $data): Parameter
    {
        return new Parameter(
            name: $data['name'],
            type: $data['type'],
            default: $data['default'],
            attributes: $data['dumpedDataMappers'],
            typeMappers: $data['dumpedTypeMappers'],
        );
    }
}
