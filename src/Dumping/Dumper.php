<?php

namespace Recoded\ObjectHydrator\Dumping;

use Recoded\ObjectHydrator\Hydration\Parameter;
use Recoded\ObjectHydrator\Hydration\Plan;
use Recoded\ObjectHydrator\Planners\DefaultPlanner;
use RuntimeException;

class Dumper
{
    /** @var array<int, class-string> */
    private array $classes = [];

    private string $planner;

    /**
     * Convert plan parameters to dumped parameters.
     *
     * @template TClass of object
     * @param \Recoded\ObjectHydrator\Hydration\Plan<TClass> $plan
     * @return \Recoded\ObjectHydrator\Hydration\Plan<TClass>
     */
    public static function convertPlanParameters(Plan $plan): Plan
    {
        return new Plan(
            initializer: $plan->initializer,
            parameters: array_map(static fn (Parameter $parameter) => new DumpedParameter(
                name: $parameter->name,
                type: $parameter->type,
                default: $parameter->default,
                attributes: $parameter->attributes,
                typeMappers: $parameter->typeMappers,
            ), $plan->parameters),
        );
    }

    /**
     * Indicate which classes to dump.
     *
     * @param array<int, class-string> $classes
     * @return $this
     */
    public function classes(array $classes): static
    {
        $this->classes = $classes;

        return $this;
    }

    /**
     * Use non-default planner.
     *
     * @param class-string<\Recoded\ObjectHydrator\Contracts\Planner> $class
     * @return $this
     */
    public function planner(string $class): static
    {
        $this->planner = $class;

        return $this;
    }

    /**
     * Write the dump to the given path.
     *
     * @param string $path
     * @return void
     */
    public function dump(string $path): void
    {
        $plannerClass = $this->planner ?? DefaultPlanner::class;

        /** @var \Recoded\ObjectHydrator\Contracts\Planner $planner */
        $planner = new $plannerClass();

        $dump = array_map(function (string $class) use ($planner) {
            return $planner->plan($class);
        }, $this->classes);

        $dump = array_combine($this->classes, $dump);

        $content = $this->makeReplacements(
            $this->getTemplate(),
            $dump,
        );

        $resource = fopen($path, 'w+');

        if ($resource === false) {
            throw new RuntimeException('Unable to open file');
        }

        try {
            $write = fwrite($resource, $content);

            if ($write === false) {
                throw new RuntimeException('Failed to write to file');
            }
        } finally {
            fclose($resource);
        }
    }

    /**
     * Get the template used to generate dumps.
     *
     * @return string
     */
    protected function getTemplate(): string
    {
        $path = __DIR__ . '/template.stub';

        $content = file_get_contents($path);

        if ($content === false) {
             throw new RuntimeException('Unable to read dump template');
        }

        return $content;
    }

    /**
     * Replace variables in the template.
     *
     * @param string $template
     * @param array<string, \Recoded\ObjectHydrator\Hydration\Plan<object>> $dump
     * @return string
     */
    protected function makeReplacements(string $template, array $dump): string
    {
        return str_replace(
            ['{{ dump }}', '{{ version }}'],
            [var_export(array_map(static::convertPlanParameters(...), $dump), true), Plan::VERSION],
            $template,
        );
    }
}
