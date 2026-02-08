<?php

namespace Squirrel\Tests\Unit\Merchant\Model;

use Merchant\Model\MerchantAccountBought;
use PHPUnit\Framework\Attributes\Test;
use Squirrel\Tests\UnitTestCase;

class MerchantAccountBoughtTest extends UnitTestCase
{
    #[Test]
    public function hydrateAndExtract(): void
    {
        $data = [
            'merchant' => 1,
            'account' => 2,
            'quantity' => 3,
        ];

        $merchantAccountBought = MerchantAccountBought::hydrate($data);

        $this->assertEquals($data, $merchantAccountBought->extract());
    }
}