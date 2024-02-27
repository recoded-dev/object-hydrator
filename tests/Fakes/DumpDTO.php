<?php

namespace Tests\Fakes;

use Recoded\ObjectHydrator\Attributes\From;

readonly class DumpDTO
{
    public function __construct(
        #[From('bar')]
        public string $foo,
    ) {
    }
}
