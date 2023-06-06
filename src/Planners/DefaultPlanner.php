<?php

namespace Recoded\ObjectHydrator\Planners;

use Recoded\ObjectHydrator\Attributes\HydrateUsing;
use Recoded\ObjectHydrator\Attributes\Initializer;
use Recoded\ObjectHydrator\Contracts\Mapping\ClassPrependableMapper;
use Recoded\ObjectHydrator\Contracts\Mapping\DataMapper;
use Recoded\ObjectHydrator\Contracts\Planner;
use Recoded\ObjectHydrator\Exceptions\ClassNotFoundException;
use Recoded\ObjectHydrator\Exceptions\InitializerMissingException;
use Recoded\ObjectHydrator\Exceptions\MultipleHydratorResolversException;
use Recoded\ObjectHydrator\Exceptions\MultipleInitializersException;
use Recoded\ObjectHydrator\Hydration\Parameter;
use Recoded\ObjectHydrator\Hydration\ParameterType;
use Recoded\ObjectHydrator\Hydration\Plan;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use Throwable;

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
     * Try to read the HydrateUsing attribute from the parameter.
     *
     * @param \ReflectionParameter $parameter
     * @return class-string<\Recoded\ObjectHydrator\Contracts\Mapping\HydratorResolver>|null
     */
    protected static function discoverResolver(ReflectionParameter $parameter): ?string
    {
        $attributes = $parameter->getAttributes(HydrateUsing::class);

        if (count($attributes) > 1) {
            throw new MultipleHydratorResolversException($parameter->getName());
        }

        if (!isset($attributes[0])) {
            return null;
        }

        /** @var \Recoded\ObjectHydrator\Attributes\HydrateUsing $attribute */
        $attribute = $attributes[0]->newInstance();

        return $attribute->hydratorResolver;
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

            $default = null;
            $type = null;

            try {
                $default = $parameter->getDefaultValue();
            } catch (Throwable) {
                //
            }

            $reflectionType = $parameter->getType();

            if ($reflectionType instanceof ReflectionNamedType && !$reflectionType->isBuiltin()) {
                /** @var class-string $typeName */
                $typeName = $reflectionType->getName();

                $type = new ParameterType(
                    name: $typeName,
                    nullable: $reflectionType->allowsNull(),
                    resolver: static::discoverResolver($parameter),
                );
            }

            return new Parameter(
                name: $parameter->getName(),
                type: $type,
                default: $default,
                attributes: array_filter($attributes, static function (object $attribute) {
                    return $attribute instanceof DataMapper;
                }),
            );
        }, $method->getParameters());
    }
}
