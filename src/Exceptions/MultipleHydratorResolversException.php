<?php

namespace Recoded\ObjectHydrator\Exceptions;

use LogicException;
use Recoded\ObjectHydrator\Contracts\HydrationException;
use Throwable;

class MultipleHydratorResolversException extends LogicException implements HydrationException
{
    public readonly string $parameter;

    /**
     * Create a new MultipleHydratorResolversException instance.
     *
     * @param string $parameter
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $parameter, int $code = 0, ?Throwable $previous = null)
    {
        $this->parameter = $parameter;

        $message = sprintf('Parameter "%s" has multiple hydrator resolvers', $parameter);

        parent::__construct($message, $code, $previous);
    }
}
