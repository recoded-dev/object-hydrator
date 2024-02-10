<?php

namespace Tests\Planners;

use ArrayAccess;
use Countable;
use PHPUnit\Framework\Attributes\CoversClass;
use Recoded\ObjectHydrator\Attributes\From;
use Recoded\ObjectHydrator\Hydration\Parameter;
use Recoded\ObjectHydrator\Hydration\ParameterType;
use Recoded\ObjectHydrator\Hydration\ParameterTypeComposition;
use Recoded\ObjectHydrator\Hydration\Plan;
use Recoded\ObjectHydrator\Planners\DefaultPlanner;
use Tests\Fakes\BarStringDTO;
use Tests\Fakes\FooBarDTO;
use Tests\Fakes\FooClassPrependableMapperStringDTO;
use Tests\Fakes\FooIntersectionDTO;
use Tests\Fakes\FooMappedStringDTO;
use Tests\Fakes\FooStringDefaultDTO;
use Tests\Fakes\FooStringDTO;
use Tests\Fakes\FooStringInitializerDTO;
use Tests\Fakes\FooUnionDTO;
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
                    type: null,
                    default: null,
                    attributes: [],
                    typeMappers: [],
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
                    type: null,
                    default: null,
                    attributes: [],
                    typeMappers: [],
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
                    type: null,
                    default: null,
                    attributes: [
                        new From('bar'),
                    ],
                    typeMappers: [],
                ),
            ],
        ), $plan);
    }

    public function test_it_discovers_class_prependable_mappers(): void
    {
        $planner = new DefaultPlanner();

        $plan = $planner->plan(FooClassPrependableMapperStringDTO::class);

        self::assertEquals(new Plan(
            initializer: null,
            parameters: [
                new Parameter(
                    name: 'foo',
                    type: null,
                    default: null,
                    attributes: [
                        new From('bar'),
                    ],
                    typeMappers: [],
                ),
            ],
        ), $plan);
    }

    public function test_it_gets_defaults(): void
    {
        $planner = new DefaultPlanner();

        $plan = $planner->plan(FooStringDefaultDTO::class);

        self::assertEquals(new Plan(
            initializer: null,
            parameters: [
                new Parameter(
                    name: 'foo',
                    type: null,
                    default: 'bar',
                    attributes: [],
                    typeMappers: [],
                ),
            ],
        ), $plan);
    }

    public function test_it_gets_types(): void
    {
        $planner = new DefaultPlanner();

        $plan = $planner->plan(FooBarDTO::class);

        self::assertEquals(new Plan(
            initializer: null,
            parameters: [
                new Parameter(
                    name: 'foo',
                    type: new ParameterType(
                        types: [BarStringDTO::class],
                        nullable: false,
                        resolver: null,
                        composition: ParameterTypeComposition::Union,
                    ),
                    default: null,
                    attributes: [],
                    typeMappers: [],
                ),
            ],
        ), $plan);
    }

    public function test_it_gets_union_types(): void
    {
        $planner = new DefaultPlanner();

        $plan = $planner->plan(FooUnionDTO::class);

        self::assertEquals(new Plan(
            initializer: null,
            parameters: [
                new Parameter(
                    name: 'foo',
                    type: new ParameterType(
                        types: [
                            BarStringDTO::class,
                            FooStringDefaultDTO::class,
                        ],
                        nullable: false,
                        resolver: null,
                        composition: ParameterTypeComposition::Union,
                    ),
                    default: null,
                    attributes: [],
                    typeMappers: [],
                ),
            ],
        ), $plan);
    }

    public function test_it_gets_intersection_types(): void
    {
        $planner = new DefaultPlanner();

        $plan = $planner->plan(FooIntersectionDTO::class);

        self::assertEquals(new Plan(
            initializer: null,
            parameters: [
                new Parameter(
                    name: 'foo',
                    type: new ParameterType(
                        types: [
                            Countable::class,
                            ArrayAccess::class,
                        ],
                        nullable: false,
                        resolver: null,
                        composition: ParameterTypeComposition::Intersection,
                    ),
                    default: null,
                    attributes: [],
                    typeMappers: [],
                ),
            ],
        ), $plan);
    }
}
