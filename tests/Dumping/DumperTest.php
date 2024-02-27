<?php

namespace Tests\Dumping;

use PHPUnit\Framework\Attributes\CoversClass;
use Recoded\ObjectHydrator\Dumping\Dumper;
use Tests\Fakes\DumpDTO;
use Tests\TestCase;

#[CoversClass(Dumper::class)]
final class DumperTest extends TestCase
{
    public function test_it_dumps(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'dump-test-');

        if ($path === false) {
            self::fail('Failed to create temp path');
        }

        (new Dumper())
            ->classes([DumpDTO::class])
            ->dump($path);

        self::assertFileEquals($path, __DIR__ . '/correct-fixture.php');
    }
}
