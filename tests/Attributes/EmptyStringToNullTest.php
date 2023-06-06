<?php

namespace Tests\Attributes;

use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use Recoded\ObjectHydrator\Attributes\SnakeCase;
use Recoded\ObjectHydrator\Contracts\Hydrator;
use Recoded\ObjectHydrator\Hydration\PlanExecutor;
use Recoded\ObjectHydrator\Planners\DefaultPlanner;
use Tests\Fakes\FooNullableStringDTO;
use Tests\TestCase;

/**
 * @todo add test for class-wide attribute.
 */
#[CoversClass(SnakeCase::class)]
final class EmptyStringToNullTest extends TestCase
{
    public function test_it_converts_empty_strings_to_null(): void
    {
        $planner = new DefaultPlanner();

        $plan = $planner->plan(FooNullableStringDTO::class);

        $executed = PlanExecutor::execute(
            FooNullableStringDTO::class,
            $plan,
            ['foo' => ''],
            Mockery::mock(Hydrator::class),
        );

        self::assertNull($executed->foo);
    }

    public function test_it_leaves_non_empty_strings(): void
    {
        $planner = new DefaultPlanner();

        $plan = $planner->plan(FooNullableStringDTO::class);

        $executed = PlanExecutor::execute(
            FooNullableStringDTO::class,
            $plan,
            ['foo' => 'some value'],
            Mockery::mock(Hydrator::class),
        );

        self::assertSame('some value', $executed->foo);
    }
}
