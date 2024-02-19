<?php

namespace Tests\Attributes;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use Recoded\ObjectHydrator\Attributes\ToDateTime;
use Tests\TestCase;

#[CoversClass(ToDateTime::class)]
final class ToDateTimeTest extends TestCase
{
    protected function tearDown(): void
    {
        ToDateTime::hydrateUsing(null);
        ToDateTime::hydrateUsingDateTimeImmutable(false);

        parent::tearDown();
    }

    public function test_it_converts_value_to_datetime(): void
    {
        $mapper = new ToDateTime();

        $result = $mapper->map('2024-01-01 12:00:00', 'foo', []);

        self::assertEquals(new DateTime('2024-01-01 12:00:00'), $result);
    }

    public function test_it_passes_timezone_as_string(): void
    {
        $mapper = new ToDateTime(timezone: 'Europe/Amsterdam');

        $result = $mapper->map('2024-01-01 12:00:00', 'foo', []);

        self::assertEquals(
            new DateTime('2024-01-01 12:00:00', new DateTimeZone('Europe/Amsterdam')),
            $result,
        );
    }

    public function test_it_passes_timezone_as_date_timezone(): void
    {
        $mapper = new ToDateTime(timezone: new DateTimeZone('Europe/Amsterdam'));

        $result = $mapper->map('2024-01-01 12:00:00', 'foo', []);

        self::assertEquals(
            new DateTime('2024-01-01 12:00:00', new DateTimeZone('Europe/Amsterdam')),
            $result,
        );
    }

    public function test_it_respects_null(): void
    {
        $mapper = new ToDateTime();

        $result = $mapper->map(null, 'foo', []);

        self::assertNull($result);
    }

    public function test_it_respects_format(): void
    {
        $mapper = new ToDateTime(format: 'Y-m-d');

        $result = $mapper->map('2024-01-01', 'foo', []);

        self::assertEquals(DateTime::createFromFormat('Y-m-d', '2024-01-01'), $result);
    }

    public function test_it_respects_format_and_null(): void
    {
        $mapper = new ToDateTime(format: 'Y-m-d');

        $result = $mapper->map(null, 'foo', []);

        self::assertNull($result);
    }

    public function test_it_respects_format_and_timezone(): void
    {
        $mapper = new ToDateTime(format: 'Y-m-d', timezone: 'Europe/Amsterdam');

        $result = $mapper->map('2024-01-01', 'foo', []);

        self::assertEquals(DateTime::createFromFormat('Y-m-d', '2024-01-01'), $result);
    }

    public function test_it_converts_value_to_datetime_immutable(): void
    {
        $mapper = new ToDateTime();
        ToDateTime::hydrateUsingDateTimeImmutable();

        $result = $mapper->map('2024-01-01 12:00:00', 'foo', []);

        self::assertEquals(new DateTimeImmutable('2024-01-01 12:00:00'), $result);
    }

    public function test_it_uses_custom_hydrator(): void
    {
        $ran = false;

        $mapper = new ToDateTime();
        ToDateTime::hydrateUsing(function () use (&$ran) {
            $ran = true;

            self::assertSame(['2024-01-01 12:00:00', null, null], func_get_args());

            return new DateTimeImmutable('2000-01-01 00:00:00');
        });

        $result = $mapper->map('2024-01-01 12:00:00', 'foo', []);

        self::assertEquals(new DateTimeImmutable('2000-01-01 00:00:00'), $result);
        self::assertTrue($ran);
    }
}
