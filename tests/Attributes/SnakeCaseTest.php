<?php

namespace Tests\Attributes;

use PHPUnit\Framework\Attributes\CoversClass;
use Recoded\ObjectHydrator\Attributes\SnakeCase;
use Recoded\ObjectHydrator\Hydration\PlanExecutor;
use Recoded\ObjectHydrator\Planners\DefaultPlanner;
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
        ));
    }
}
