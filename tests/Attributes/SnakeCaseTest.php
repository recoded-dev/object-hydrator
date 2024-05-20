<?php

namespace Tests\Attributes;

use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use Recoded\ObjectHydrator\Attributes\SnakeCase;
use Recoded\ObjectHydrator\Contracts\Hydrator;
use Recoded\ObjectHydrator\Hydration\PlanExecutor;
use Recoded\ObjectHydrator\Planners\DefaultPlanner;
use Tests\Fakes\Attributes\FooBarStringSnakeCaseClassDTO;
use Tests\Fakes\Attributes\FooBarStringSnakeCaseDTO;
use Tests\TestCase;

/**
 * @todo add test for class-wide attribute.
 */
#[CoversClass(SnakeCase::class)]
final class SnakeCaseTest extends TestCase
{
    public function test_it_gets_it_correctly(): void
    {
        $planner = new DefaultPlanner();

        $plan = $planner->plan(FooBarStringSnakeCaseDTO::class);

        self::assertEquals(new FooBarStringSnakeCaseDTO(
            fooBar: 'baz',
        ), PlanExecutor::execute(
            FooBarStringSnakeCaseDTO::class,
            $plan,
            ['foo_bar' => 'baz'],
            Mockery::mock(Hydrator::class),
        ));
    }

    public function test_resets_previous(): void
    {
        $planner = new DefaultPlanner();

        $plan = $planner->plan(FooBarStringSnakeCaseClassDTO::class);

        self::assertEquals(new FooBarStringSnakeCaseClassDTO(
            fooBar: 'baz',
            foo: 'baz',
        ), PlanExecutor::execute(
            FooBarStringSnakeCaseClassDTO::class,
            $plan,
            ['foo_bar' => 'baz'],
            Mockery::mock(Hydrator::class),
        ));
    }
}
