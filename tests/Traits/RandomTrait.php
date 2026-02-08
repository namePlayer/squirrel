<?php

declare(strict_types=1);

namespace Squirrel\Tests\Traits;

trait RandomTrait
{
    public function setStaticRandomSeed(int $seed = 5): void
    {
        mt_srand($seed);
    }
}
