<?php

namespace Tests\Fakes;

use Recoded\ObjectHydrator\Attributes\From;

readonly class FooMappedStringDTO
{
    public function __construct(
        #[From('bar')]
        public string $foo,
    ) {
    }
}
