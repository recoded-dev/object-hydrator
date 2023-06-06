<?php

namespace Tests\Fakes;

use Recoded\ObjectHydrator\Attributes\From;

#[From('bar')]
readonly class FooClassPrependableMapperStringDTO
{
    public function __construct(
        public string $foo,
    ) {
    }
}
