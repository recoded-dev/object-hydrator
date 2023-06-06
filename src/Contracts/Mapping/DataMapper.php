<?php

namespace Recoded\ObjectHydrator\Contracts\Mapping;

interface DataMapper
{
    /**
     * Map data.
     *
     * @param mixed $value
     * @param string $name
     * @param array<array-key, mixed>|object $data
     * @return mixed
     */
    public function map(mixed $value, string $name, array|object $data): mixed;
}
