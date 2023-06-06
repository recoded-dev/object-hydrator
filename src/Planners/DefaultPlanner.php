<?php

namespace Recoded\ObjectHydrator\Planners;

use Recoded\ObjectHydrator\Attributes\Initializer;
use Recoded\ObjectHydrator\Contracts\Mapping\ClassPrependableMapper;
use Recoded\ObjectHydrator\Contracts\Mapping\DataMapper;
use Recoded\ObjectHydrator\Contracts\Planner;
use Recoded\ObjectHydrator\Exceptions\ClassNotFoundException;
use Recoded\ObjectHydrator\Exceptions\InitializerMissingException;
use Recoded\ObjectHydrator\Exceptions\MultipleInitializersException;
use Recoded\ObjectHydrator\Hydration\Parameter;
use Recoded\ObjectHydrator\Hydration\Plan;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionParameter;

class DefaultPlanner implements Planner
{
    /**
     * Create a hydration plan for a class.
     *
     * @template T of object
     * @param class-string<T> $class
     * @return \Recoded\ObjectHydrator\Hydration\Plan<T>
     */
    public function plan(string $class): Plan
    {
        if (!class_exists($class)) {
            throw new ClassNotFoundException($class);
        }

        // note to self: hydration plans
        // dump plan to file, add version/hash of plan class (vendor) to dump, scan statically before, else exception
        //
        $class = new ReflectionClass($class);

        $initializer = $this->discoverInitializer($class);

        $prepends = $this->discoverPrepends($class);

        $parameters = $this->mapParameters($class, $initializer, $prepends);

        return new Plan(
            initializer: $initializer,
            parameters: $parameters,
        );
    }

    /**
     * Try to read the Initializer attribute from the class.
     *
     * @param \ReflectionClass<object> $class
     * @return string|null
     */
    protected function discoverInitializer(ReflectionClass $class): ?string
    {
        $attributes = $class->getAttributes(Initializer::class);

        if (count($attributes) > 1) {
            throw new MultipleInitializersException($class->getName());
        }

        if (!isset($attributes[0])) {
            return null;
        }

        /** @var \Recoded\ObjectHydrator\Attributes\Initializer $initializer */
        $initializer = $attributes[0]->newInstance();

        return $initializer->method;
    }

    /**
     * Get all ClassPrependableMapper attributes on the class.
     *
     * @param \ReflectionClass<object> $class
     * @return array<int, \Recoded\ObjectHydrator\Contracts\Mapping\ClassPrependableMapper>
     */
    protected function discoverPrepends(ReflectionClass $class): array
    {
        $attributes = array_map(static function (ReflectionAttribute $attribute) {
            return $attribute->newInstance();
        }, $class->getAttributes());

        return array_filter($attributes, static function (object $attribute) {
            return $attribute instanceof ClassPrependableMapper;
        });
    }

    /**
     * @param \ReflectionClass<object> $class
     * @param string|null $initializer
     * @param array<int, \Recoded\ObjectHydrator\Contracts\Mapping\ClassPrependableMapper> $prepends
     * @return \Recoded\ObjectHydrator\Hydration\Parameter[]
     */
    protected function mapParameters(ReflectionClass $class, ?string $initializer, array $prepends): array
    {
        $initializer ??= '__construct';

        if (!$class->hasMethod($initializer)) {
            throw new InitializerMissingException($class->getName(), $initializer);
        }

        $method = $class->getMethod($initializer);

        return array_map(static function (ReflectionParameter $parameter) use ($prepends) {
            $attributes = array_map(static function (ReflectionAttribute $attribute) {
                return $attribute->newInstance();
            }, $parameter->getAttributes());

            $attributes = array_merge($prepends, $attributes);

            return new Parameter(
                name: $parameter->getName(),
                attributes: array_filter($attributes, static function (object $attribute) {
                    return $attribute instanceof DataMapper;
                }),
            );
        }, $method->getParameters());
    }
}
