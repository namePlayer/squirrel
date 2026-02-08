<?php

declare(strict_types=1);

namespace Squirrel\Tests;

use App\Software;
use League\Container\Container;
use PHPUnit\Framework\TestCase;

class IntegrationTestCase extends TestCase
{
    protected Container $container;

    protected function setUp(): void
    {
        $this->init();

        parent::setUp();
    }

    protected function tearDown(): void
    {
        unset($this->container);

        parent::tearDown();
    }

    private function init(): void
    {
        Software::initEnvironment(Software::BASE_DIR . '/.env.test');

        require __DIR__ . '/../config/container.php';
        assert(isset($container) && $container instanceof Container);

        $this->container = $container;
    }
}
