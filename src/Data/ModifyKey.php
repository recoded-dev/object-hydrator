<?php

namespace Recoded\ObjectHydrator\Data;

final readonly class ModifyKey
{
    /**
     * Create a new ModifyKey instance.
     *
     * @param string $key
     * @param bool $fromRoot
     * @return void
     */
    public function __construct(
        public string $key,
        public bool $fromRoot = false,
    ) {
    }
}
