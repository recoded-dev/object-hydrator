<?php

namespace Recoded\ObjectHydrator\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class HydrateUsing
{
    /**
     * @param class-string<\Recoded\ObjectHydrator\Contracts\Mapping\HydratorResolver>|null $hydratorResolver
     */
    public function __construct(public ?string $hydratorResolver)
    {
    }
}
