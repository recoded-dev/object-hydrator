<?php

namespace Recoded\ObjectHydrator\Hydration;

use Recoded\ObjectHydrator\Contracts\Hydrator;
use Recoded\ObjectHydrator\Contracts\Planner;
use Recoded\ObjectHydrator\Planners\DefaultPlanner;

class ReflectionHydrator implements Hydrator
{
    protected Planner $planner;

    /**
     * Get the planner, default if unset.
     *
     * @return \Recoded\ObjectHydrator\Contracts\Planner
     */
    protected function getPlanner(): Planner
    {
        return $this->planner ??= new DefaultPlanner();
    }

    /**
     * Hydrate an object from raw data.
     *
     * @template T of object
     * @param array<array-key, mixed> $data
     * @param class-string<T> $type
     * @return T
     */
    public function hydrate(array $data, string $type): object
    {
        $plan = $this->getPlanner()->plan($type);

        return PlanExecutor::execute($type, $plan, $data);
    }
}
