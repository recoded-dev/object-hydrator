<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Recoded\ObjectHydrator\Hydration\PlanExecutor;

abstract class TestCase extends BaseTestCase
{
    protected function tearDown(): void
    {
        PlanExecutor::executeNormally();

        parent::tearDown();
    }
}
