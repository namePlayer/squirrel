<?php
declare(strict_types=1);

namespace Merchant\DTO;

class BuyOfferDTO
{

    public function __construct(
        public readonly int $offerId,
        public readonly int $accountId,
        public readonly int $quantity
    )
    {
    }

}
