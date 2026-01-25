<?php
declare(strict_types=1);

namespace Merchant\Service;

use App\Exception\Resource\ResourceDoesNotExistException;
use App\Service\Resource\ResourceService;
use Merchant\DTO\CreateOfferDTO;
use Merchant\Exception\MerchantOfferCouldNotBeCreatedException;
use Merchant\Model\Merchant;
use Merchant\Table\MerchantTable;
use Ramsey\Uuid\Uuid;

class MerchantOfferService
{

    public function __construct(
        private readonly MerchantTable $merchantTable,
        private readonly ResourceService $resourceService,
    )
    {
    }

    public function create(CreateOfferDTO $createOfferDTO): Merchant
    {
        if($this->resourceService->getResourceByUid($createOfferDTO->resource) === null)
        {
            throw new ResourceDoesNotExistException();
        }

        $merchant = new Merchant();
        do {
            $merchant->slug = Uuid::uuid4();
        } while($this->getOfferBySlug($merchant->slug->toString()) instanceof Merchant);
        $merchant->resource = $createOfferDTO->resource;
        $merchant->price = $createOfferDTO->price;
        $merchant->quantity = $createOfferDTO->quantity;
        $merchant->expires = $createOfferDTO->expires;
        if($this->merchantTable->insert($merchant))
        {
            return $this->getOfferBySlug($merchant->slug->toString());
        }
        throw new MerchantOfferCouldNotBeCreatedException();
    }

    public function getAllCurrentOffers(): array
    {
        $currentTime = new \DateTime();
        return $this->merchantTable->findAllByExpiresGreaterThan($currentTime);
    }

    public function getOfferBySlug(string $slug): ?Merchant
    {
        return $this->merchantTable->findBySlug($slug);
    }

}
