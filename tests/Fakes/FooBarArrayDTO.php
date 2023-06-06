<?php

namespace Tests\Fakes;

use Recoded\ObjectHydrator\Attributes\MapArrayToType;

readonly class FooBarArrayDTO
{
    /**
     * @param array<array-key, \Tests\Fakes\BarStringDTO> $foo
     * @return void
     */
    public function __construct(
        #[MapArrayToType(BarStringDTO::class)]
        public array $foo,
    ) {
    }
}
