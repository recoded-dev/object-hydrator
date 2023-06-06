<?php

namespace Recoded\ObjectHydrator\Contracts;

use Recoded\ObjectHydrator\Hydration\Plan;

interface Planner
{
    /**
     * Create a hydration plan for a class.
     *
     * @template T of object
     * @param class-string<T> $class
     * @return \Recoded\ObjectHydrator\Hydration\Plan<T>
     */
    public function plan(string $class): Plan;
}
