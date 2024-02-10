<?php

namespace Tests\Attributes;

use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use Recoded\ObjectHydrator\Attributes\TypeFromKey;
use Recoded\ObjectHydrator\Contracts\Hydrator;
use Recoded\ObjectHydrator\Hydration\PlanExecutor;
use Recoded\ObjectHydrator\Planners\DefaultPlanner;
use Tests\Fakes\Attributes\FooUnionTypeFromKeyDTO;
use Tests\Fakes\BarStringDTO;
use Tests\Fakes\FooStringDefaultDTO;
use Tests\TestCase;

#[CoversClass(TypeFromKey::class)]
final class TypeFromKeyTest extends TestCase
{
    public function test_it_falls_back_to_value_when_type_null(): void
    {
        $planner = new DefaultPlanner();

        $plan = $planner->plan(FooUnionTypeFromKeyDTO::class);

        $executed = PlanExecutor::execute(
            FooUnionTypeFromKeyDTO::class,
            $plan,
            [
                'foo' => [
                    'type' => 'unknown_type',
                ],
            ],
            Mockery::mock(Hydrator::class),
        );

        self::assertSame([
            'type' => 'unknown_type',
        ], $executed->foo);
    }

    public function test_it_hydrates_type_one(): void
    {
        $planner = new DefaultPlanner();

        $plan = $planner->plan(FooUnionTypeFromKeyDTO::class);

        $hydrator = Mockery::mock(Hydrator::class);
        $hydrator
            ->expects('hydrate')
            ->with([
                'type' => 'foo',
            ], FooStringDefaultDTO::class)
            ->andReturn($expected = new FooStringDefaultDTO(foo: 'expected'));

        $executed = PlanExecutor::execute(
            FooUnionTypeFromKeyDTO::class,
            $plan,
            [
                'foo' => [
                    'type' => 'foo',
                ],
            ],
            $hydrator,
        );

        self::assertSame($expected, $executed->foo);
    }

    public function test_it_hydrates_type_two(): void
    {
        $planner = new DefaultPlanner();

        $plan = $planner->plan(FooUnionTypeFromKeyDTO::class);

        $hydrator = Mockery::mock(Hydrator::class);
        $hydrator
            ->expects('hydrate')
            ->with([
                'type' => 'bar',
            ], BarStringDTO::class)
            ->andReturn($expected = new BarStringDTO(bar: 'expected'));

        $executed = PlanExecutor::execute(
            FooUnionTypeFromKeyDTO::class,
            $plan,
            [
                'foo' => [
                    'type' => 'bar',
                ],
            ],
            $hydrator,
        );

        self::assertSame($expected, $executed->foo);
    }
}
