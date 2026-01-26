<?php
declare(strict_types=1);

namespace Merchant\Service;

use App\Exception\Account\AccountNotFoundException;
use App\Exception\Account\MoneyCanNotBeLessThanZeroException;
use App\Exception\Account\MoneyCouldNotBeDepositedToAccountException;
use App\Exception\Account\MoneyCouldNotBeWithdrawnFromAccountException;
use App\Exception\Inventory\ResourceCouldNotBeAddedToInventoryException;
use App\Exception\Resource\ResourceDoesNotExistException;
use App\Service\Account\AccountService;
use App\Service\Economy\InventoryService;
use App\Service\Economy\MoneyService;
use App\Service\Resource\ResourceService;
use Merchant\DTO\BuyOfferDTO;
use Merchant\Exception\MerchantInvalidOfferException;
use Merchant\Exception\MerchantOfferBuyQuantityCanNotBeZeroOrLessException;
use Merchant\Exception\MerchantOfferCouldNotBeFoundException;

class MerchantTransactionService
{

    public function __construct(
        private readonly MerchantOfferService $merchantOfferService,
        private readonly MoneyService $moneyService,
        private readonly InventoryService $inventoryService,
        private readonly ResourceService $resourceService,
        private readonly AccountService $accountService,
    )
    {
    }

    public function buyItemFromOffer(BuyOfferDTO $buyOfferDTO): void
    {
        $offer = $this->merchantOfferService->getOfferById($buyOfferDTO->offerId);
        if($offer === null){
            throw new MerchantOfferCouldNotBeFoundException();
        }

        $account = $this->accountService->getAccountById($buyOfferDTO->accountId);
        if($account === null) {
            throw new AccountNotFoundException();
        }

        try {
            $resource = $this->resourceService->getResourceDetailsByUid($offer->resource);
        } catch (ResourceDoesNotExistException $e) {
            throw new MerchantInvalidOfferException(previous: $e);
        }

        if($buyOfferDTO->quantity < 1)
        {
            throw new MerchantOfferBuyQuantityCanNotBeZeroOrLessException();
        }

        $buyingPrice = $offer->price * $buyOfferDTO->quantity;

        try {
            $this->moneyService->withdrawMoneyFromAccount($account->id, $buyingPrice);
            $this->inventoryService->addToInventory($account->id, $offer->resource, $buyOfferDTO->quantity);
        } catch (AccountNotFoundException|MoneyCanNotBeLessThanZeroException|MoneyCouldNotBeWithdrawnFromAccountException|ResourceDoesNotExistException $e) {
            throw $e;
        } catch (ResourceCouldNotBeAddedToInventoryException $e) {
            $this->moneyService->depositMoneyToAccount($account->id, $buyingPrice);
            throw $e;
        }
    }

}
