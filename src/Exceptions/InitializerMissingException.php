<?php

namespace Recoded\ObjectHydrator\Exceptions;

use LogicException;
use Recoded\ObjectHydrator\Contracts\HydrationException;
use Throwable;

class InitializerMissingException extends LogicException implements HydrationException
{
    public readonly string $class;
    public readonly string $initializer;

    /**
     * Create a new InitializerMissingException instance.
     *
     * @param class-string $class
     * @param string $initializer
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $class, string $initializer, int $code = 0, ?Throwable $previous = null)
    {
        $this->class = $class;
        $this->initializer = $initializer;

        $message = sprintf('Initializer "%s" missing on class "%s"', $initializer, $class);

        parent::__construct($message, $code, $previous);
    }
}
