<?php

namespace Tests\Fakes\Attributes;

use Recoded\ObjectHydrator\Attributes\From;
use Recoded\ObjectHydrator\Attributes\SnakeCase;

#[SnakeCase]
readonly class FooBarStringSnakeCaseClassDTO
{
    public function __construct(
        public string $fooBar,
        #[From('foo_bar')]
        public string $foo,
    ) {
    }
}
