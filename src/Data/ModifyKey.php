<?php

namespace Recoded\ObjectHydrator\Data;

final readonly class ModifyKey
{
    /**
     * Create a new ModifyKey instance.
     *
     * @param string $key
     * @param bool $fromRoot
     * @param bool $resetPreceding
     * @return void
     */
    public function __construct(
        public string $key,
        public bool $fromRoot = false,
        public bool $resetPreceding = false,
    ) {
    }
}
