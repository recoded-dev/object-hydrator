<?php

namespace Recoded\ObjectHydrator\Contracts\Mapping;

interface OrderAware
{
    /**
     * Indicate what order this is called as.
     *
     * @param int $order
     * @return void
     */
    public function setOrder(int $order): void;
}
