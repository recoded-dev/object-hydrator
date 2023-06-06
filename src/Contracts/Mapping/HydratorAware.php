<?php

namespace Recoded\ObjectHydrator\Contracts\Mapping;

use Recoded\ObjectHydrator\Contracts\Hydrator;

interface HydratorAware
{
    /**
     * Set the hydrator.
     *
     * @param \Recoded\ObjectHydrator\Contracts\Hydrator $hydrator
     * @return void
     */
    public function setHydrator(Hydrator $hydrator): void;
}
