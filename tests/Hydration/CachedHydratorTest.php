<?php

namespace Tests\Hydration;

use PHPUnit\Framework\Attributes\CoversClass;
use Recoded\ObjectHydrator\Hydration\CachedHydrator;
use RuntimeException;
use Tests\Fakes\DumpDTO;
use Tests\Fakes\FooStringDTO;
use Tests\TestCase;

#[CoversClass(CachedHydratorTest::class)]
final class CachedHydratorTest extends TestCase
{
    public function test_it_loads_cache_and_verifies_version(): void
    {
        $hydrator = new CachedHydrator(__DIR__ . '/fixtures/cached-outdated.php');

        $this->expectExceptionObject(new RuntimeException('Invalid cached plan version.'));

        $hydrator->hydrate([], FooStringDTO::class);
    }

    public function test_it_hydrates_objects(): void
    {
        $hydrator = new CachedHydrator(__DIR__ . '/fixtures/cached.php');

        $hydrated = $hydrator->hydrate(['foo' => 'bar'], FooStringDTO::class);

        self::assertEquals(new FooStringDTO(foo: 'bar'), $hydrated);
    }

    public function test_it_hydrates_objects_with_dumped_attributes(): void
    {
        $hydrator = new CachedHydrator(__DIR__ . '/fixtures/cached-attributes.php');

        $hydrated = $hydrator->hydrate(['bar' => 'baz'], DumpDTO::class);

        self::assertEquals(new DumpDTO(foo: 'baz'), $hydrated);
    }
}
