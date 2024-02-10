<?php

namespace Tests\Hydration;

use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use Recoded\ObjectHydrator\Contracts\Planner;
use Recoded\ObjectHydrator\Hydration\Plan;
use Recoded\ObjectHydrator\Hydration\PlanExecutor;
use Recoded\ObjectHydrator\Hydration\ReflectionHydrator;
use Tests\Fakes\FooStringDTO;
use Tests\TestCase;

#[CoversClass(ReflectionHydrator::class)]
final class ReflectionHydratorTest extends TestCase
{
    public function test_it_plans_and_executes(): void
    {
        $planner = Mockery::mock(Planner::class);

        $hydrator = new class ($planner) extends ReflectionHydrator {
            public function __construct(Planner $planner)
            {
                $this->planner = $planner;
            }
        };

        $planner
            ->expects('plan')
            ->with(FooStringDTO::class)
            ->andReturn(new Plan(null, []));

        $expected = new FooStringDTO(foo: 'bar');

        PlanExecutor::executeUsing(fn () => $expected);

        $hydrated = $hydrator->hydrate([], FooStringDTO::class);

        self::assertSame($expected, $hydrated);
    }
}
