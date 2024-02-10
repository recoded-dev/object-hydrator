<?php

namespace Recoded\ObjectHydrator\Contracts\Mapping;

interface TypeMapper
{
    /**
     * Map type of parameter.
     *
     * @param string|null $type
     * @param array<array-key, mixed>|object $data
     * @return class-string|null
     */
    public function map(?string $type, array|object $data): ?string;
}
