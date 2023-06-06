<?php

namespace Tests\Fakes;

readonly class FooNullableBarDTO
{
    public function __construct(public ?BarStringDTO $foo)
    {
    }
}
