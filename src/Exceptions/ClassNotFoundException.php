<?php

namespace Recoded\ObjectHydrator\Exceptions;

use Recoded\ObjectHydrator\Contracts\HydrationException;
use RuntimeException;
use Throwable;

class ClassNotFoundException extends RuntimeException implements HydrationException
{
    public readonly string $class;

    /**
     * Create a new ClassNotFoundException instance.
     *
     * @param class-string $class
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $class, int $code = 0, ?Throwable $previous = null)
    {
        $this->class = $class;

        $message = sprintf('Class "%s" not found', $class);

        parent::__construct($message, $code, $previous);
    }
}
