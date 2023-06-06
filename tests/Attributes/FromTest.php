<?php

namespace Tests\Attributes;

use PHPUnit\Framework\Attributes\CoversClass;
use Recoded\ObjectHydrator\Attributes\From;
use Recoded\ObjectHydrator\Hydration\PlanExecutor;
use Recoded\ObjectHydrator\Planners\DefaultPlanner;
use Tests\Fakes\Attributes\FooStringFromDTO;
use Tests\Fakes\Attributes\FooStringMultipleFromDTO;
use Tests\Fakes\Attributes\FooStringNestedFromDTO;
use Tests\TestCase;

#[CoversClass(From::class)]
final class FromTest extends TestCase
{
    public function test_it_gets_it_correctly(): void
    {
        $planner = new DefaultPlanner();

        $plan = $planner->plan(FooStringFromDTO::class);

        self::assertEquals(new FooStringFromDTO(
            foo: 'bar',
        ), PlanExecutor::execute(
            FooStringFromDTO::class,
            $plan,
            ['a' => 'bar'],
        ));
    }

    public function test_it_gets_it_correctly_when_multiple(): void
    {
        $planner = new DefaultPlanner();

        $plan = $planner->plan(FooStringMultipleFromDTO::class);

        self::assertEquals(new FooStringMultipleFromDTO(
            foo: 'bar',
        ), PlanExecutor::execute(
            FooStringMultipleFromDTO::class,
            $plan,
            ['a' => ['b' => 'bar']],
        ));
    }

    public function test_it_gets_it_correctly_when_nested(): void
    {
        $planner = new DefaultPlanner();

        $plan = $planner->plan(FooStringNestedFromDTO::class);

        self::assertEquals(new FooStringNestedFromDTO(
            foo: 'bar',
        ), PlanExecutor::execute(
            FooStringNestedFromDTO::class,
            $plan,
            ['a' => ['b' => 'bar']],
        ));
    }
}
