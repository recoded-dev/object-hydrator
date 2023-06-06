<?php

namespace Tests\Fakes\Attributes;

use Recoded\ObjectHydrator\Attributes\From;

readonly class FooStringMultipleFromDTO
{
    public function __construct(
        #[From('a')]
        #[From('b')]
        public string $foo,
    ) {
    }
}
