<?php

namespace Tests\Fakes\Attributes;

use Recoded\ObjectHydrator\Attributes\From;

readonly class FooStringFromDTO
{
    public function __construct(
        #[From('a')]
        public string $foo,
    ) {
    }
}
