<?php
declare(strict_types=1);

namespace Merchant\Service;

use App\DTO\Resource\ResourceDTO;
use App\Enum\Resource\ItemGroupEnum;
use App\Exception\Resource\ResourceDoesNotExistException;
use App\Service\RandomService;
use App\Service\Resource\ResourceService;
use App\Software;
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
        private readonly RandomService $randomService,
    )
    {
    }

    public function create(CreateOfferDTO $createOfferDTO): Merchant
    {
        try {
            $resource = $this->resourceService->getResourceDetailsByUid($createOfferDTO->resource);
        } catch (ResourceDoesNotExistException $e) {
            throw new ResourceDoesNotExistException();
        }

        $merchant = new Merchant();
        do {
            $merchant->slug = Uuid::uuid4();
        } while($this->getOfferBySlug($merchant->slug->toString()) instanceof Merchant);

        $quantity = $createOfferDTO->quantity;
        if($quantity === null)
        {
            $quantity = $this->randomService->generateRandomIntegerInRange(
                $resource->merchantMinOffer, $resource->merchantMaxOffer);
        }
        $merchant->quantity = $quantity;

        $price = $createOfferDTO->price;
        if($price === null)
        {
            $price = $resource->priceBuy;
        }
        $merchant->price = $price;

        $merchant->resource = $createOfferDTO->resource;
        $merchant->expires = $createOfferDTO->expires;

        if($this->merchantTable->insert($merchant))
        {
            return $this->getOfferBySlug($merchant->slug->toString());
        }
        throw new MerchantOfferCouldNotBeCreatedException();
    }

    public function generateMerchantOffers(int $amount): array
    {
        $resources = $this->resourceService->getResourcesFromYaml();
        $resourceOfferChances = [];
        $offers = [];
        foreach ($resources as $properties)
        {
            /* @var ResourceDTO $properties */
            if(($properties->merchantOfferProbability <= 0) ||
                ($properties->merchantMinOffer <= 0) ||
                ($properties->merchantMaxOffer <= 0) ||
                (in_array(ItemGroupEnum::NotOfferedByMerchant, $properties->itemGroups)))
            {
                continue;
            }

            $offerChance = floor($this->randomService->generateRandomIntegerInRange(0, 100) * $properties->merchantOfferProbability);
            while(isset($resources[$offerChance]))
            {
                $offerChance -= 1;
            }
            $resourceOfferChances[$offerChance] = $properties;
        }

        krsort($resourceOfferChances);
        $counter = 0;
        foreach ($resourceOfferChances as $offerChance => $properties)
        {
            if($counter >= $amount)
            {
                break;
            }

            $offers[$properties->uid] = $properties;
            unset($resourceOfferChances[$offerChance]);

            $offer = new CreateOfferDTO(
                $properties->uid,
                new \DateTime()->modify('+'.Software::DEFAULT_MERCHANT_OFFER_LIFETIME),
                null,
                null
            );
            try {
                $this->create($offer);
            } catch (ResourceDoesNotExistException|MerchantOfferCouldNotBeCreatedException $e) {}
            $counter++;
        }

        return $offers;
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

    public function getOfferById(int $offerId): ?Merchant
    {
        return $this->merchantTable->findById($offerId);
    }

}
