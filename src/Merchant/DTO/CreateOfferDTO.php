<?php
declare(strict_types=1);

namespace Merchant\DTO;

class CreateOfferDTO
{

    public function __construct(
        public readonly string $resource,
        public readonly int $price,
        public readonly int $quantity,
        public readonly \DateTime $expires,
    )
    {
    }

}
