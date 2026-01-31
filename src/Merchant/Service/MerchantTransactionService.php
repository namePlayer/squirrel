<?php
declare(strict_types=1);

namespace Merchant\Service;

use App\Exception\Account\AccountNotFoundException;
use App\Exception\Account\MoneyCanNotBeLessThanZeroException;
use App\Exception\Account\MoneyCouldNotBeDepositedToAccountException;
use App\Exception\Account\MoneyCouldNotBeWithdrawnFromAccountException;
use App\Exception\Inventory\AccountResourceAmountCantBeLessThanZeroException;
use App\Exception\Inventory\AccountResourceIsNotInInventoryException;
use App\Exception\Inventory\ResourceCouldNotBeAddedToInventoryException;
use App\Exception\Inventory\ResourceCouldNotBeTakenFromInventoryException;
use App\Exception\Resource\ResourceDoesNotExistException;
use App\Service\Account\AccountService;
use App\Service\Economy\InventoryService;
use App\Service\Economy\MoneyService;
use App\Service\Resource\ResourceService;
use Merchant\DTO\BuyOfferDTO;
use Merchant\Exception\MerchantAccountOfferBoughtCouldNotBeTrackedException;
use Merchant\Exception\MerchantAccountWouldExceedOfferLimitException;
use Merchant\Exception\MerchantInvalidOfferException;
use Merchant\Exception\MerchantOfferBuyQuantityCanNotBeZeroOrLessException;
use Merchant\Exception\MerchantOfferCouldNotBeFoundException;
use Merchant\Model\MerchantAccountBought;
use Merchant\Table\MerchantAccountBoughtTable;

class MerchantTransactionService
{

    public function __construct(
        private readonly MerchantOfferService $merchantOfferService,
        private readonly MoneyService $moneyService,
        private readonly InventoryService $inventoryService,
        private readonly ResourceService $resourceService,
        private readonly AccountService $accountService,
        private readonly MerchantAccountBoughtTable $merchantAccountBoughtTable,
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
            $this->registerBoughtFromMerchant($buyOfferDTO);
            $this->moneyService->withdrawMoneyFromAccount($account->id, $buyingPrice);
            $this->inventoryService->addToInventory($account->id, $offer->resource, $buyOfferDTO->quantity);
        } catch (ResourceCouldNotBeAddedToInventoryException $e) {
            $this->moneyService->depositMoneyToAccount($account->id, $buyingPrice);
            throw $e;
        } catch (AccountNotFoundException|MoneyCanNotBeLessThanZeroException
            |MoneyCouldNotBeWithdrawnFromAccountException|ResourceDoesNotExistException
            |MerchantAccountWouldExceedOfferLimitException|MerchantOfferCouldNotBeFoundException
            |MerchantAccountOfferBoughtCouldNotBeTrackedException $e) {
            throw $e;
        }
    }

    public function registerBoughtFromMerchant(BuyOfferDTO $buyOfferDTO): void
    {
        $create = false;
        $offerHistory = $this->getOfferBoughtByAccount($buyOfferDTO->offerId, $buyOfferDTO->accountId);
        if($offerHistory === null){
            $create = true;
            $offerHistory = new MerchantAccountBought();
            $offerHistory->account = $buyOfferDTO->accountId;
            $offerHistory->merchant = $buyOfferDTO->offerId;
            $offerHistory->quantity = 0;
        }
        $offerHistory->quantity += $buyOfferDTO->quantity;

        $offer = $this->merchantOfferService->getOfferById($buyOfferDTO->offerId);
        if($offer === null){
            throw new MerchantOfferCouldNotBeFoundException();
        }

        try {
            $resource = $this->resourceService->getResourceDetailsByUid($offer->resource);
        } catch (ResourceDoesNotExistException $e) {
            throw new ResourceDoesNotExistException();
        }

        if($offerHistory->quantity > $offer->quantity)
        {
            throw new MerchantAccountWouldExceedOfferLimitException();
        }

        if($create)
        {
            if(false === $this->merchantAccountBoughtTable->insert($offerHistory))
            {
                throw new MerchantAccountOfferBoughtCouldNotBeTrackedException();
            }
            return;
        }

        if(false === $this->merchantAccountBoughtTable->update($offerHistory))
        {
            throw new MerchantAccountOfferBoughtCouldNotBeTrackedException();
        }
    }

    public function sellFromInventory(int $accountId, string $resourceUid, int $quantity = 1): void
    {
        try {
            $inventoryItem = $this->inventoryService->getAccountInventoryItemAmount($accountId, $resourceUid);
        } catch (AccountNotFoundException|ResourceDoesNotExistException $e) {
            throw $e;
        }

        if($inventoryItem === null)
        {
            throw new AccountResourceIsNotInInventoryException();
        }

        try {
            $resource = $this->resourceService->getResourceDetailsByUid($inventoryItem->resource);
        } catch (ResourceDoesNotExistException $e) {
            throw $e;
        }

        $inventoryItem->quantity -= $quantity;
        if($inventoryItem->quantity < 0)
        {
            throw new AccountResourceAmountCantBeLessThanZeroException();
        }

        $payoutAmount = $resource->priceSell * $quantity;

        try {
            $this->inventoryService->takeFromInventory($accountId, $resourceUid, $quantity);
            $this->moneyService->depositMoneyToAccount($accountId, $payoutAmount);
        } catch (AccountNotFoundException|AccountResourceAmountCantBeLessThanZeroException
            |AccountResourceIsNotInInventoryException|ResourceCouldNotBeTakenFromInventoryException
            |ResourceDoesNotExistException|MoneyCouldNotBeDepositedToAccountException $e) {
            throw $e;
        }
    }

    public function getOfferBoughtByAccount(int $offerId, int $accountId): ?MerchantAccountBought
    {
        return $this->merchantAccountBoughtTable->findByMerchantAndAccount($offerId, $accountId);
    }

}
