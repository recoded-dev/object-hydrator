<?php

namespace Tests\Attributes;

use Hamcrest\Matchers;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use Recoded\ObjectHydrator\Attributes\MapArrayToType;
use Recoded\ObjectHydrator\Contracts\Hydrator;
use Recoded\ObjectHydrator\Hydration\PlanExecutor;
use Recoded\ObjectHydrator\Planners\DefaultPlanner;
use Tests\Fakes\BarStringDTO;
use Tests\Fakes\FooBarArrayDTO;
use Tests\TestCase;
use UnexpectedValueException;

#[CoversClass(MapArrayToType::class)]
final class MapArrayToTypeTest extends TestCase
{
    public function test_it_hydrates_correctly(): void
    {
        $planner = new DefaultPlanner();

        $plan = $planner->plan(FooBarArrayDTO::class);

        $hydrator = Mockery::mock(Hydrator::class);

        $hydrator
            ->expects('hydrate')
            ->ordered()
            ->with(['bar' => '1'], BarStringDTO::class)
            ->andReturn(new BarStringDTO(bar: '1'));

        $hydrator
            ->expects('hydrate')
            ->ordered()
            ->with(Matchers::equalTo((object) ['bar' => '2']), BarStringDTO::class)
            ->andReturn(new BarStringDTO(bar: '2'));

        $data = [
            'foo' => [
                [
                    'bar' => '1',
                ],
                (object) [
                    'bar' => '2',
                ],
            ],
        ];

        $executed = PlanExecutor::execute(FooBarArrayDTO::class, $plan, $data, $hydrator);

        self::assertEquals(new FooBarArrayDTO(
            foo: [
                new BarStringDTO(bar: '1'),
                new BarStringDTO(bar: '2'),
            ],
        ), $executed);
    }

    public function test_it_preserves_keys(): void
    {
        $planner = new DefaultPlanner();

        $plan = $planner->plan(FooBarArrayDTO::class);

        $hydrator = Mockery::mock(Hydrator::class);

        $hydrator
            ->expects('hydrate')
            ->ordered()
            ->with(['bar' => '1'], BarStringDTO::class)
            ->andReturn(new BarStringDTO(bar: '1'));

        $hydrator
            ->expects('hydrate')
            ->ordered()
            ->with(['bar' => '2'], BarStringDTO::class)
            ->andReturn(new BarStringDTO(bar: '2'));

        $data = [
            'foo' => [
                'first' => ['bar' => '1'],
                'second' => ['bar' => '2'],
            ],
        ];

        $executed = PlanExecutor::execute(FooBarArrayDTO::class, $plan, $data, $hydrator);

        self::assertEquals(new FooBarArrayDTO(
            foo: [
                'first' => new BarStringDTO(bar: '1'),
                'second' => new BarStringDTO(bar: '2'),
            ],
        ), $executed);
    }

    public function test_it_throws_exception_when_array_item_is_not_array_or_object(): void
    {
        $planner = new DefaultPlanner();

        $plan = $planner->plan(FooBarArrayDTO::class);

        $hydrator = Mockery::mock(Hydrator::class);

        $hydrator
            ->expects('hydrate')
            ->ordered()
            ->with(['bar' => '1'], BarStringDTO::class)
            ->andReturn(new BarStringDTO(bar: '1'));

        $data = [
            'foo' => [
                ['bar' => '1'],
                null,
            ],
        ];

        $this->expectExceptionObject(new UnexpectedValueException('Expected array or object in array, got: null'));

        PlanExecutor::execute(FooBarArrayDTO::class, $plan, $data, $hydrator);
    }
}
