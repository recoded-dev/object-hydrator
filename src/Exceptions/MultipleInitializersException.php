<?php

namespace Recoded\ObjectHydrator\Exceptions;

use LogicException;
use Recoded\ObjectHydrator\Contracts\HydrationException;
use Throwable;

class MultipleInitializersException extends LogicException implements HydrationException
{
    public readonly string $class;

    /**
     * Create a new MultipleInitializersException instance.
     *
     * @param class-string $class
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $class, int $code = 0, ?Throwable $previous = null)
    {
        $this->class = $class;

        $message = sprintf('Class "%s" has multiple hydration initializers', $class);

        parent::__construct($message, $code, $previous);
    }
}
