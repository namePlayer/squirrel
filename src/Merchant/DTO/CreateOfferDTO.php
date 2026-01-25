<?php
declare(strict_types=1);

namespace Merchant\DTO;

class CreateOfferDTO
{

    public function __construct(
        public readonly string $resource,
        public readonly \DateTime $expires,
        public readonly ?int $quantity,
        public readonly ?int $price,
    )
    {
    }

}
