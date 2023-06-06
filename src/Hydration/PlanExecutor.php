<?php

namespace Recoded\ObjectHydrator\Hydration;

use Closure;
use Recoded\ObjectHydrator\Contracts\Mapping\DataMapper;
use Recoded\ObjectHydrator\Contracts\Mapping\OrderAware;

final class PlanExecutor
{
    private static ?Closure $executeUsing = null;

    /**
     * Execute a plan.
     *
     * @template T of object
     * @param class-string<T> $class
     * @param \Recoded\ObjectHydrator\Hydration\Plan<T> $plan
     * @param array<array-key, mixed> $data
     * @return T
     */
    private static function default(string $class, Plan $plan, array $data): object
    {
        $parameters = array_map(function (Parameter $parameter) use ($data) {
            $i = 0;

            return array_reduce($parameter->attributes, function (mixed $carry, DataMapper $mapper) use ($data, &$i) {
                $i++;

                if ($mapper instanceof OrderAware) {
                    $mapper->setOrder($i);
                }

                return $mapper->map($carry, $data);
            }, $data[$parameter->name] ?? null);
        }, $plan->parameters);

        if ($plan->initializer === null) {
            return new $class(...$parameters);
        }

        return $class::{$plan->initializer}(...$parameters);
    }

    /**
     * Execute a plan.
     *
     * @template T of object
     * @param class-string<T> $class
     * @param \Recoded\ObjectHydrator\Hydration\Plan<T> $plan
     * @param array<array-key, mixed> $data
     * @return T
     */
    public static function execute(string $class, Plan $plan, array $data): object
    {
        if (self::$executeUsing !== null) {
            /** @var T */ // phpcs:ignore

            return call_user_func(self::$executeUsing, $class, $plan, $data);
        }

        return self::default($class, $plan, $data);
    }

    /**
     * Specify custom execution behaviour.
     *
     * @template TClass of object
     * @param (callable(string, \Recoded\ObjectHydrator\Hydration\Plan<TClass>, array<array-key, mixed>): TClass) $executeUsing
     * @return void
     */
    public static function executeUsing(callable $executeUsing): void
    {
        self::$executeUsing = $executeUsing(...);
    }

    /**
     * Unset the callable from the executeUsing method.
     *
     * @see self::executeUsing
     * @return void
     */
    public static function executeNormally(): void
    {
        self::$executeUsing = null;
    }
}
