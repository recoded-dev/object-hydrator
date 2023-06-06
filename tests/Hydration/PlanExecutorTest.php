<?php

namespace Tests\Hydration;

use PHPUnit\Framework\Attributes\CoversClass;
use Recoded\ObjectHydrator\Attributes\From;
use Recoded\ObjectHydrator\Hydration\Parameter;
use Recoded\ObjectHydrator\Hydration\Plan;
use Recoded\ObjectHydrator\Hydration\PlanExecutor;
use Recoded\ObjectHydrator\Planners\DefaultPlanner;
use Tests\Fakes\FooMappedStringDTO;
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

        PlanExecutor::executeUsing(function (string $class, Plan $planned, array $data) use ($hydrated, $plan, &$ran) {
            $ran = true;

            self::assertSame(FooStringDTO::class, $class);
            self::assertSame($plan, $planned);
            self::assertSame([], $data);

            return $hydrated;
        });

        $executed = PlanExecutor::execute(FooStringDTO::class, $plan, []);

        self::assertSame($hydrated, $executed);
        self::assertTrue($ran);
    }

    public function testItHydratesUsingDefaultExecutor(): void
    {
        $executed = PlanExecutor::execute(
            class: FooMappedStringDTO::class,
            plan: (new DefaultPlanner())->plan(FooMappedStringDTO::class),
            data: ['bar' => 'expected'],
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
                    default: 'bar',
                    attributes: [],
                ),
            ],
        );

        $executed = PlanExecutor::execute(
            class: FooStringDefaultDTO::class,
            plan: $plan,
            data: ['foo' => null],
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
                    default: 'bar',
                    attributes: [],
                ),
            ],
        );

        $executed = PlanExecutor::execute(
            class: FooStringDefaultDTO::class,
            plan: $plan,
            data: [],
        );

        self::assertEquals(new FooStringDefaultDTO(
            foo: 'bar',
        ), $executed);
    }
}
