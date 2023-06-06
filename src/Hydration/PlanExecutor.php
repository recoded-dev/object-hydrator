<?php

namespace Recoded\ObjectHydrator\Hydration;

use ArrayAccess;
use Closure;
use Recoded\ObjectHydrator\Contracts\Mapping\DataMapper;
use Recoded\ObjectHydrator\Data\ModifyKey;

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
            $name = $parameter->name;
            $precedingValue = $data;

            $value = array_reduce(
                $parameter->attributes,
                function (mixed $carry, DataMapper $mapper) use ($data, $name, &$precedingValue) {
                    $mapped = $mapper->map($carry, $name, $data);

                    if ($mapped instanceof ModifyKey) {
                        $from = $mapped->fromRoot ? $data : $precedingValue;
                        $key = $mapped->key;

                        $mapped = self::get($from, $key);
                    }

                    return $precedingValue = $mapped;
                },
                self::get($data, $name),
            );

            return $value ?? $parameter->default;
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

    /**
     * Get key from data as far as possible, supports arrays and objects.
     *
     * @param mixed $value
     * @param string $key
     * @return mixed
     */
    protected static function get(mixed $value, string $key): mixed
    {
        $parts = preg_split('/(?<!\\\)\./', $key);

        if ($parts === false) {
            return $value;
        }

        foreach ($parts as $part) {
            $part = str_replace('\.', '.', $part);

            if (is_array($value) || $value instanceof ArrayAccess) {
                $value = $value[$part] ?? null;

                continue;
            }

            if (is_object($value)) {
                $value = $value->{$part} ?? null;

                continue;
            }

            return $value;
        }

        return $value;
    }
}
