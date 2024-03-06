<?php

namespace Tests\Hydration;

use ArrayObject;
use Exception;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Recoded\ObjectHydrator\Attributes\From;
use Recoded\ObjectHydrator\Contracts\Hydrator;
use Recoded\ObjectHydrator\Contracts\Mapping\TypeMapper;
use Recoded\ObjectHydrator\Hydration\Parameter;
use Recoded\ObjectHydrator\Hydration\ParameterType;
use Recoded\ObjectHydrator\Hydration\ParameterTypeComposition;
use Recoded\ObjectHydrator\Hydration\Plan;
use Recoded\ObjectHydrator\Hydration\PlanExecutor;
use Recoded\ObjectHydrator\Planners\DefaultPlanner;
use stdClass;
use Tests\Fakes\BarBackedEnumDTO;
use Tests\Fakes\BarStringDTO;
use Tests\Fakes\BarUnitEnumDTO;
use Tests\Fakes\FooBarDTO;
use Tests\Fakes\FooMappedStringDTO;
use Tests\Fakes\FooNullableBarDTO;
use Tests\Fakes\FooNullableDefaultStringDTO;
use Tests\Fakes\FooStringDefaultDTO;
use Tests\Fakes\FooStringDTO;
use Tests\Fakes\FooUnionDTO;
use Tests\Fakes\Value\FooBackedEnum;
use Tests\Fakes\Value\FooUnitEnum;
use Tests\TestCase;
use TypeError;

#[CoversClass(PlanExecutor::class)]
final class PlanExecutorTest extends TestCase
{
    public function test_it_hydrates_using_custom_callable(): void
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

    public function test_it_hydratesUsing_default_executor(): void
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

    public function test_it_throws_type_errors(): void
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
                    typeMappers: [],
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

    public function test_it_fills_null_when_null_with_default(): void
    {
        $plan = new Plan(
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
        );

        $executed = PlanExecutor::execute(
            class: FooNullableDefaultStringDTO::class,
            plan: $plan,
            data: ['foo' => null],
            hydrator: Mockery::mock(Hydrator::class),
        );

        self::assertEquals(new FooNullableDefaultStringDTO(
            foo: null,
        ), $executed);
    }

    /**
     * @param array<array-key, mixed>|object $data
     * @return void
     */
    #[TestWith([[]])]
    #[TestWith([new ArrayObject()])]
    #[TestWith([new stdClass()])]
    public function test_it_fills_defaults_when_unset(array|object $data): void
    {
        $plan = new Plan(
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
        );

        $executed = PlanExecutor::execute(
            class: FooStringDefaultDTO::class,
            plan: $plan,
            data: $data,
            hydrator: Mockery::mock(Hydrator::class),
        );

        self::assertEquals(new FooStringDefaultDTO(
            foo: 'bar',
        ), $executed);
    }

    public function test_it_maps_to_non_built_in_types(): void
    {
        $plan = new Plan(
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
                    default: 'bar',
                    attributes: [],
                    typeMappers: [],
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

    public function test_it_maps_to_non_built_in_nullable_types(): void
    {
        $plan = new Plan(
            initializer: null,
            parameters: [
                new Parameter(
                    name: 'foo',
                    type: new ParameterType(
                        types: [BarStringDTO::class],
                        nullable: true,
                        resolver: null,
                        composition: ParameterTypeComposition::Union,
                    ),
                    default: null,
                    attributes: [],
                    typeMappers: [],
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

    public function test_it_does_not_call_sub_hydrator_when_value_is_not_std_class_when_is_object(): void
    {
        $plan = new Plan(
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
                    default: 'bar',
                    attributes: [],
                    typeMappers: [],
                ),
            ],
        );

        $subHydrator = Mockery::mock(Hydrator::class);

        $subHydrator
            ->expects('hydrate')
            ->never()
            ->andThrow(Exception::class, 'Should not hydrate');

        $executed = PlanExecutor::execute(
            class: FooBarDTO::class,
            plan: $plan,
            data: ['foo' => new BarStringDTO(bar: 'baz')],
            hydrator: $subHydrator,
        );

        self::assertEquals(new FooBarDTO(
            foo: new BarStringDTO(
                bar: 'baz',
            ),
        ), $executed);
    }

    public function test_it_calls_type_mappers_and_return_null(): void
    {
        $mapper = Mockery::mock(TypeMapper::class);

        $plan = new Plan(
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
                    typeMappers: [$mapper],
                ),
            ],
        );

        $mapper
            ->expects('map')
            ->with(BarStringDTO::class, [
                'type' => 'bar',
            ])
            ->andReturnNull();

        $this->expectException(TypeError::class);

        PlanExecutor::execute(
            class: FooUnionDTO::class,
            plan: $plan,
            data: [
                'foo' => [
                    'type' => 'bar',
                ],
            ],
            hydrator: Mockery::mock(Hydrator::class),
        );
    }

    public function test_it_calls_type_mappers_and_return_type(): void
    {
        $mapper = Mockery::mock(TypeMapper::class);

        $plan = new Plan(
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
                    typeMappers: [$mapper],
                ),
            ],
        );

        $mapper
            ->expects('map')
            ->with(BarStringDTO::class, [
                'type' => 'bar',
                'foo' => 'expected',
            ])
            ->andReturn(FooStringDefaultDTO::class);

        $hydrator = Mockery::mock(Hydrator::class);
        $hydrator
            ->expects('hydrate')
            ->with([
                'type' => 'bar',
                'foo' => 'expected',
            ], FooStringDefaultDTO::class)
            ->andReturn(new FooStringDefaultDTO(foo: 'expected'));

        $executed = PlanExecutor::execute(
            class: FooUnionDTO::class,
            plan: $plan,
            data: [
                'foo' => [
                    'type' => 'bar',
                    'foo' => 'expected',
                ],
            ],
            hydrator: $hydrator,
        );

        self::assertEquals(new FooUnionDTO(
            foo: new FooStringDefaultDTO(foo: 'expected'),
        ), $executed);
    }

    public function test_it_hydrates_backed_enums(): void
    {
        $plan = new Plan(
            initializer: null,
            parameters: [
                new Parameter(
                    name: 'bar',
                    type: new ParameterType(
                        types: [FooBackedEnum::class],
                        nullable: false,
                        resolver: null,
                        composition: ParameterTypeComposition::Union,
                    ),
                    default: null,
                    attributes: [],
                    typeMappers: [],
                ),
            ],
        );

        $executed = PlanExecutor::execute(
            class: BarBackedEnumDTO::class,
            plan: $plan,
            data: [
                'bar' => 'foo',
            ],
            hydrator: Mockery::mock(Hydrator::class),
        );

        self::assertSame(FooBackedEnum::Foo, $executed->bar);
    }

    public function test_it_hydrates_unit_enums(): void
    {
        $plan = new Plan(
            initializer: null,
            parameters: [
                new Parameter(
                    name: 'bar',
                    type: new ParameterType(
                        types: [FooUnitEnum::class],
                        nullable: false,
                        resolver: null,
                        composition: ParameterTypeComposition::Union,
                    ),
                    default: null,
                    attributes: [],
                    typeMappers: [],
                ),
            ],
        );

        $executed = PlanExecutor::execute(
            class: BarUnitEnumDTO::class,
            plan: $plan,
            data: [
                'bar' => 'Foo',
            ],
            hydrator: Mockery::mock(Hydrator::class),
        );

        self::assertSame(FooUnitEnum::Foo, $executed->bar);
    }
}
