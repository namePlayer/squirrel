<?php

namespace Squirrel\Tests\Unit\Merchant\DTO;

use Merchant\DTO\BuyOfferDTO;
use PHPUnit\Framework\Attributes\Test;
use Squirrel\Tests\UnitTestCase;

class BuyOfferDTOTest extends UnitTestCase
{
    #[Test]
    public function all(): void
    {
        $offerId = 1;
        $accountId = 2;
        $quantity = 3;

        $dto = new BuyOfferDTO($offerId, $accountId, $quantity);

        $this->assertEquals($offerId, $dto->offerId);
        $this->assertEquals($accountId, $dto->accountId);
        $this->assertEquals($quantity, $dto->quantity);
    }
}
