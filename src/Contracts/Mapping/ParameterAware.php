<?php

namespace Recoded\ObjectHydrator\Contracts\Mapping;

use Recoded\ObjectHydrator\Hydration\Parameter;

interface ParameterAware
{
    /**
     * Set the current parameter.
     *
     * @param \Recoded\ObjectHydrator\Hydration\Parameter $parameter
     * @return void
     */
    public function setParameter(Parameter $parameter): void;
}
