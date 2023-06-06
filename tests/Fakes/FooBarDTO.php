<?php

namespace Tests\Fakes;

readonly class FooBarDTO
{
    public function __construct(public BarStringDTO $foo)
    {
    }
}
