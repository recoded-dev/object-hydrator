<?php

namespace Tests\Fakes;

readonly class FooStringDefaultDTO
{
    public function __construct(public string $foo = 'bar')
    {
    }
}
