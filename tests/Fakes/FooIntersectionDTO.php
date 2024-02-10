<?php

namespace Tests\Fakes;

use ArrayAccess;
use Countable;

readonly class FooIntersectionDTO
{
    /**
     * @param \Countable|\ArrayAccess $foo
     * @phpstan-param \Countable&\ArrayAccess<array-key, mixed> $foo
     * @return void
     */
    public function __construct(
        public Countable&ArrayAccess $foo,
    ) {
    }
}
