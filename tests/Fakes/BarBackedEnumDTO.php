<?php

namespace Tests\Fakes;

use Tests\Fakes\Value\FooBackedEnum;

readonly class BarBackedEnumDTO
{
    public function __construct(public FooBackedEnum $bar)
    {
    }
}
