<?php

namespace Tests\Fakes\Attributes;

use Recoded\ObjectHydrator\Attributes\SnakeCase;

readonly class FooBarStringSnakeCaseDTO
{
    public function __construct(
        #[SnakeCase]
        public string $fooBar,
    ) {
    }
}
