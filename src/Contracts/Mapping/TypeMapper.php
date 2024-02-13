<?php

namespace Recoded\ObjectHydrator\Contracts\Mapping;

interface TypeMapper
{
    /**
     * Map type of parameter.
     *
     * @param class-string|null $type
     * @param mixed $data
     * @return class-string|null
     */
    public function map(?string $type, mixed $data): ?string;
}
