<?php

namespace Tests\Fakes\Attributes;

use Recoded\ObjectHydrator\Attributes\NullToDefault;

readonly class FooStringNullToDefaultDTO
{
    public function __construct(
        #[NullToDefault]
        public string $foo = 'bar',
    ) {
    }
}
