<?php

namespace App\DTO\Resource;

class ResourceDTO
{

    public function __construct(
        public readonly string $uid,
        public int $goldBuy = 0,
        public int $goldSell = 0,
        public bool $merchantAlwaysOffer = false,
        public int $merchantMinOffer = 0,
        public int $merchantMaxOffer = 1
    )
    {
    }

}
