<?php

namespace Tests\Hydration;

use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use Recoded\ObjectHydrator\Attributes\From;
use Recoded\ObjectHydrator\Contracts\Hydrator;
use Recoded\ObjectHydrator\Hydration\Parameter;
use Recoded\ObjectHydrator\Hydration\ParameterType;
use Recoded\ObjectHydrator\Hydration\Plan;
use Recoded\ObjectHydrator\Hydration\PlanExecutor;
use Recoded\ObjectHydrator\Planners\DefaultPlanner;
use Tests\Fakes\BarStringDTO;
use Tests\Fakes\FooBarDTO;
use Tests\Fakes\FooMappedStringDTO;
use Tests\Fakes\FooNullableBarDTO;
use Tests\Fakes\FooStringDefaultDTO;
use Tests\Fakes\FooStringDTO;
use Tests\TestCase;
use TypeError;

#[CoversClass(PlanExecutor::class)]
final class PlanExecutorTest extends TestCase
{
    public function testItHydratesUsingCustomCallable(): void
    {
        $hydrated = new FooStringDTO(foo: 'bar');
        $plan = new Plan(null, []);

        $ran = false;

        PlanExecutor::executeUsing(
            function (string $class, Plan $planned, array|object $data) use ($hydrated, $plan, &$ran) {
                $ran = true;

                self::assertSame(FooStringDTO::class, $class);
                self::assertSame($plan, $planned);
                self::assertSame([], $data);

                return $hydrated;
            },
        );

        $executed = PlanExecutor::execute(
            FooStringDTO::class,
            $plan,
            [],
            Mockery::mock(Hydrator::class),
        );

        self::assertSame($hydrated, $executed);
        self::assertTrue($ran);
    }

    public function testItHydratesUsingDefaultExecutor(): void
    {
        $executed = PlanExecutor::execute(
            class: FooMappedStringDTO::class,
            plan: (new DefaultPlanner())->plan(FooMappedStringDTO::class),
            data: ['bar' => 'expected'],
            hydrator: Mockery::mock(Hydrator::class),
        );

        self::assertEquals(new FooMappedStringDTO(
            foo: 'expected',
        ), $executed);
    }

    public function testItThrowsTypeErrors(): void
    {
        $plan = new Plan(
            initializer: null,
            parameters: [
                new Parameter(
                    name: 'foo',
                    type: null,
                    default: null,
                    attributes: [
                        new From('foo.baz'),
                    ],
                ),
            ],
        );

        try {
            PlanExecutor::execute(
                class: FooMappedStringDTO::class,
                plan: $plan,
                data: ['baz' => 'expected'],
                hydrator: Mockery::mock(Hydrator::class),
            );

            self::fail('TypeError should have been encountered.');
        } catch (TypeError) {
            $this->addToAssertionCount(1);
        }
    }

    public function testItFillsDefaultsWhenNull(): void
    {
        $plan = new Plan(
            initializer: null,
            parameters: [
                new Parameter(
                    name: 'foo',
                    type: null,
                    default: 'bar',
                    attributes: [],
                ),
            ],
        );

        $executed = PlanExecutor::execute(
            class: FooStringDefaultDTO::class,
            plan: $plan,
            data: ['foo' => null],
            hydrator: Mockery::mock(Hydrator::class),
        );

        self::assertEquals(new FooStringDefaultDTO(
            foo: 'bar',
        ), $executed);
    }

    public function testItFillsDefaultsWhenUnset(): void
    {
        $plan = new Plan(
            initializer: null,
            parameters: [
                new Parameter(
                    name: 'foo',
                    type: null,
                    default: 'bar',
                    attributes: [],
                ),
            ],
        );

        $executed = PlanExecutor::execute(
            class: FooStringDefaultDTO::class,
            plan: $plan,
            data: [],
            hydrator: Mockery::mock(Hydrator::class),
        );

        self::assertEquals(new FooStringDefaultDTO(
            foo: 'bar',
        ), $executed);
    }

    public function testMapsToNonBuiltInTypes(): void
    {
        $plan = new Plan(
            initializer: null,
            parameters: [
                new Parameter(
                    name: 'foo',
                    type: new ParameterType(
                        name: BarStringDTO::class,
                        nullable: false,
                        resolver: null,
                    ),
                    default: 'bar',
                    attributes: [],
                ),
            ],
        );

        $subHydrator = Mockery::mock(Hydrator::class);

        $subHydrator
            ->expects('hydrate')
            ->with(['bar' => 'baz'], BarStringDTO::class)
            ->andReturn(new BarStringDTO(
                bar: 'baz',
            ));

        $executed = PlanExecutor::execute(
            class: FooBarDTO::class,
            plan: $plan,
            data: ['foo' => ['bar' => 'baz']],
            hydrator: $subHydrator,
        );

        self::assertEquals(new FooBarDTO(
            foo: new BarStringDTO(
                bar: 'baz',
            ),
        ), $executed);
    }

    public function testMapsToNonBuiltInNullableTypes(): void
    {
        $plan = new Plan(
            initializer: null,
            parameters: [
                new Parameter(
                    name: 'foo',
                    type: new ParameterType(
                        name: BarStringDTO::class,
                        nullable: true,
                        resolver: null,
                    ),
                    default: null,
                    attributes: [],
                ),
            ],
        );

        $executed = PlanExecutor::execute(
            class: FooNullableBarDTO::class,
            plan: $plan,
            data: ['foo' => null],
            hydrator: Mockery::mock(Hydrator::class),
        );

        self::assertEquals(new FooNullableBarDTO(foo: null), $executed);
    }
}
