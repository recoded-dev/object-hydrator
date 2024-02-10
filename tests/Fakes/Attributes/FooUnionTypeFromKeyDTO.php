<?php

namespace Tests\Fakes\Attributes;

use Recoded\ObjectHydrator\Attributes\TypeFromKey;
use Tests\Fakes\BarStringDTO;
use Tests\Fakes\FooStringDefaultDTO;

readonly class FooUnionTypeFromKeyDTO
{
    /**
     * @param array<string, string>|\Tests\Fakes\BarStringDTO|\Tests\Fakes\FooStringDefaultDTO $foo
     */
    public function __construct(
        #[TypeFromKey('type', [
            'foo' => FooStringDefaultDTO::class,
            'bar' => BarStringDTO::class,
        ])]
        public array|BarStringDTO|FooStringDefaultDTO $foo,
    ) {
    }
}
