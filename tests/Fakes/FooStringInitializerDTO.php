<?php

namespace Tests\Fakes;

use Recoded\ObjectHydrator\Attributes\Initializer;

#[Initializer('create')]
final readonly class FooStringInitializerDTO
{
    public function __construct(public string $foo)
    {
    }

    public static function create(string $bar): self
    {
        return new self($bar);
    }
}
