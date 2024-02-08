<?php

namespace Tests\Fakes;

readonly class FooNullableDefaultStringDTO
{
    public function __construct(public ?string $foo = 'bar')
    {
    }
}
