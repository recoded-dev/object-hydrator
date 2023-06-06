<?php

namespace Tests\Planners;

use PHPUnit\Framework\Attributes\CoversClass;
use Recoded\ObjectHydrator\Attributes\From;
use Recoded\ObjectHydrator\Hydration\Parameter;
use Recoded\ObjectHydrator\Hydration\Plan;
use Recoded\ObjectHydrator\Planners\DefaultPlanner;
use Tests\Fakes\FooMappedStringDTO;
use Tests\Fakes\FooStringDTO;
use Tests\Fakes\FooStringInitializerDTO;
use Tests\TestCase;

#[CoversClass(DefaultPlanner::class)]
final class DefaultPlannerTest extends TestCase
{
    public function test_it_plans_with_initializer(): void
    {
        $planner = new DefaultPlanner();

        $plan = $planner->plan(FooStringInitializerDTO::class);

        self::assertEquals(new Plan(
            initializer: 'create',
            parameters: [
                new Parameter(
                    name: 'bar',
                    attributes: [],
                ),
            ],
        ), $plan);
    }

    public function test_it_plans_without_initializer(): void
    {
        $planner = new DefaultPlanner();

        $plan = $planner->plan(FooStringDTO::class);

        self::assertEquals(new Plan(
            initializer: null,
            parameters: [
                new Parameter(
                    name: 'foo',
                    attributes: [],
                ),
            ],
        ), $plan);
    }

    public function test_it_discovers_mapping_attributes(): void
    {
        $planner = new DefaultPlanner();

        $plan = $planner->plan(FooMappedStringDTO::class);

        self::assertEquals(new Plan(
            initializer: null,
            parameters: [
                new Parameter(
                    name: 'foo',
                    attributes: [
                        new From('bar'),
                    ],
                ),
            ],
        ), $plan);
    }
}
