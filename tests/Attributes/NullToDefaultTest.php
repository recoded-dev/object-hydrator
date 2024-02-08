<?php

namespace Tests\Attributes;

use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use Recoded\ObjectHydrator\Attributes\NullToDefault;
use Recoded\ObjectHydrator\Contracts\Hydrator;
use Recoded\ObjectHydrator\Hydration\PlanExecutor;
use Recoded\ObjectHydrator\Planners\DefaultPlanner;
use Tests\Fakes\Attributes\FooStringNullToDefaultDTO;
use Tests\TestCase;

/**
 * @todo add test for class-wide attribute.
 */
#[CoversClass(NullToDefault::class)]
final class NullToDefaultTest extends TestCase
{
    public function test_it_gets_it_correctly(): void
    {
        $plan = (new DefaultPlanner())->plan(FooStringNullToDefaultDTO::class);

        self::assertEquals(new FooStringNullToDefaultDTO(
            foo: 'bar',
        ), PlanExecutor::execute(
            FooStringNullToDefaultDTO::class,
            $plan,
            ['foo' => null],
            Mockery::mock(Hydrator::class),
        ));
    }
}
