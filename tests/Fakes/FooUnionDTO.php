<?php

namespace Tests\Fakes;

readonly class FooUnionDTO
{
    public function __construct(
        public false|BarStringDTO|FooStringDefaultDTO $foo,
    ) {
    }
}
