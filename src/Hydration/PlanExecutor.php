<?php

namespace Recoded\ObjectHydrator\Hydration;

use ArrayAccess;
use BackedEnum;
use Closure;
use Recoded\ObjectHydrator\Contracts\Hydrator;
use Recoded\ObjectHydrator\Contracts\Mapping\DataMapper;
use Recoded\ObjectHydrator\Contracts\Mapping\HydratorAware;
use Recoded\ObjectHydrator\Contracts\Mapping\ParameterAware;
use Recoded\ObjectHydrator\Contracts\Mapping\TypeMapper;
use Recoded\ObjectHydrator\Data\ModifyKey;
use stdClass;
use UnitEnum;

final class PlanExecutor
{
    private static ?Closure $executeUsing = null;

    /**
     * Execute a plan.
     *
     * @template T of object
     * @param class-string<T> $class
     * @param \Recoded\ObjectHydrator\Hydration\Plan<T> $plan
     * @param array<array-key, mixed>|object $data
     * @param \Recoded\ObjectHydrator\Contracts\Hydrator $hydrator
     * @return T
     */
    private static function default(string $class, Plan $plan, array|object $data, Hydrator $hydrator): object
    {
        $parameters = array_map(function (Parameter $parameter) use ($data, $hydrator) {
            $name = $parameter->name;
            $precedingValue = $data;

            $value = array_reduce(
                $parameter->attributes,
                function (
                    mixed $carry,
                    DataMapper $mapper,
                ) use (
                    $data,
                    &$hydrator,
                    $name,
                    $parameter,
                    &$precedingValue,
                ) {
                    if ($mapper instanceof HydratorAware) {
                        $mapper->setHydrator($hydrator);
                    }

                    if ($mapper instanceof ParameterAware) {
                        $mapper->setParameter($parameter);
                    }

                    $mapped = $mapper->map($carry, $name, $data);

                    if ($mapped instanceof ModifyKey) {
                        $resetPreceding = $mapped->resetPreceding;
                        $from = $mapped->fromRoot ? $data : $precedingValue;
                        $key = $mapped->key;

                        $mapped = self::get($from, $key, $parameter->default);

                        if ($resetPreceding) {
                            $precedingValue = $from;

                            return $mapped;
                        }
                    }

                    return $precedingValue = $mapped;
                },
                self::get($data, $name, $parameter->default),
            );

            if ($parameter->type !== null || $parameter->typeMappers !== []) {
                // TODO find out why reference
                if ($parameter->type?->resolver !== null) {
                    $hydrator = $parameter->type->resolver::resolve($data);
                }

                $type = array_reduce(
                    $parameter->typeMappers,
                    static fn (?string $type, TypeMapper $mapper) => $mapper->map($type, $value),
                    $parameter->type?->main(),
                );

                if ($type !== null) {
                    if (is_a($type, UnitEnum::class, true) && (is_int($value) || is_string($value))) {
                        if (is_a($type, BackedEnum::class, true)) {
                            $value = $type::from($value);
                        } elseif (is_string($value)) {
                            $value = constant("$type::$value");
                        }
                    } else {
                        if (!is_object($value) && !is_array($value)) {
                            return null;
                        }

                        if (is_object($value) && $value::class !== stdClass::class) {
                            return $value;
                        }

                        $value = $hydrator->hydrate($value, $type);
                    }
                }
            }

            return $value;
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
     * @param array<array-key, mixed>|object $data
     * @param \Recoded\ObjectHydrator\Contracts\Hydrator $hydrator
     * @return T
     */
    public static function execute(string $class, Plan $plan, array|object $data, Hydrator $hydrator): object
    {
        if (self::$executeUsing !== null) {
            /** @var T */ // phpcs:ignore

            return call_user_func(self::$executeUsing, $class, $plan, $data, $hydrator);
        }

        return self::default($class, $plan, $data, $hydrator);
    }

    /**
     * Specify custom execution behaviour.
     *
     * @template TClass of object
     * @param (callable(string, \Recoded\ObjectHydrator\Hydration\Plan<TClass>, array<array-key, mixed>|object, \Recoded\ObjectHydrator\Contracts\Hydrator): TClass) $executeUsing
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
     * @param mixed $default
     * @return mixed
     * @internal
     */
    public static function get(mixed $value, string $key, mixed $default = null): mixed
    {
        $parts = preg_split('/(?<!\\\)\./', $key);

        if ($parts === false) {
            return $value;
        }

        foreach ($parts as $part) {
            $part = str_replace('\.', '.', $part);

            if (is_array($value)) {
                if (!array_key_exists($part, $value)) {
                    return $default;
                }

                $value = $value[$part];

                continue;
            }

            if ($value instanceof ArrayAccess) {
                if (!$value->offsetExists($part)) {
                    return $default;
                }

                $value = $value->offsetGet($part);

                continue;
            }

            if (is_object($value)) {
                if (!property_exists($value, $part)) {
                    return $default;
                }

                $value = $value->{$part};

                continue;
            }

            return $value;
        }

        return $value;
    }
}
