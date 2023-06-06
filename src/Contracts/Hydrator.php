<?php

namespace Recoded\ObjectHydrator\Contracts;

interface Hydrator
{
    /**
     * Hydrate an object from raw data.
     *
     * @template T of object
     * @param array<array-key, mixed>|object $data
     * @param class-string<T> $type
     * @return T
     */
    public function hydrate(array|object $data, string $type): object;
}
