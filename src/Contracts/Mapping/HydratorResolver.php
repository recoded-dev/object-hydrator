<?php

namespace Recoded\ObjectHydrator\Contracts\Mapping;

use Recoded\ObjectHydrator\Contracts\Hydrator;

interface HydratorResolver
{
    /**
     * Resolve a hydrator from given data.
     *
     * @param array<array-key, mixed>|object $data
     * @return \Recoded\ObjectHydrator\Contracts\Hydrator
     */
    public static function resolve(array|object $data): Hydrator;
}
