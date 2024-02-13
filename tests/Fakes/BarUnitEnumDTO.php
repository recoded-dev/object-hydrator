<?php

namespace Tests\Fakes;

use Tests\Fakes\Value\FooUnitEnum;

readonly class BarUnitEnumDTO
{
    public function __construct(public FooUnitEnum $bar)
    {
    }
}
