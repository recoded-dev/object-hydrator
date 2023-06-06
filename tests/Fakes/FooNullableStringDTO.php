<?php

namespace Tests\Fakes;

use Recoded\ObjectHydrator\Attributes\EmptyStringToNull;

readonly class FooNullableStringDTO
{
    public function __construct(
        #[EmptyStringToNull]
        public ?string $foo,
    ) {
    }
}
