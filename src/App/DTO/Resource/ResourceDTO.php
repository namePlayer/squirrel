<?php

namespace App\DTO\Resource;

class ResourceDTO
{

    public function __construct(
        public readonly string $uid,
        public int             $priceBuy = 0,
        public int             $priceSell = 0,
        public array           $itemGroups = [],
        public bool            $merchantKeepInStockGroup = false,
        public float           $merchantOfferProbability = 1.0,
        public int             $merchantMinOffer = 0,
        public int             $merchantMaxOffer = 0
    )
    {
    }

}
