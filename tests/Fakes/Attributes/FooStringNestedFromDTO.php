<?php

namespace Tests\Fakes\Attributes;

use Recoded\ObjectHydrator\Attributes\From;

readonly class FooStringNestedFromDTO
{
    public function __construct(
        #[From('a.b')]
        public string $foo,
    ) {
    }
}
