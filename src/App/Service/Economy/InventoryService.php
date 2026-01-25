<?php
declare(strict_types=1);

namespace App\Service\Economy;

use App\Exception\Account\AccountNotFoundException;
use App\Exception\Inventory\AccountResourceAmountCantBeLessThanZeroException;
use App\Exception\Inventory\AccountResourceCouldNotBeUpdatedException;
use App\Exception\Inventory\AccountResourceIsNotInInventoryException;
use App\Exception\Inventory\ResourceCouldNotBeAddedToInventoryException;
use App\Exception\Inventory\ResourceCouldNotBeTakenFromInventoryException;
use App\Exception\Resource\ResourceDoesNotExistException;
use App\Model\AccountResource;
use App\Service\Account\AccountService;
use App\Service\Resource\ResourceService;
use App\Table\Resource\AccountResourceTable;

class InventoryService
{

    public function __construct(
        private readonly AccountResourceTable $accountResourceTable,
        private readonly AccountService $accountService,
        private readonly ResourceService $resourceService
    )
    {
    }

    private function update(AccountResource $accountResource): void
    {
        if(false === $this->accountResourceTable->update($accountResource))
        {
            throw new AccountResourceCouldNotBeUpdatedException();
        }
    }

    public function addToInventory(int $accountId, string $resourceUid, int $amount): void
    {
        if($this->accountService->getAccountById($accountId) === null)
        {
            throw new AccountNotFoundException();
        }

        if($this->resourceService->getResourceByUid($resourceUid) === null)
        {
            throw new ResourceDoesNotExistException();
        }

        $accountResource = $this->getAccountInventoryItemAmount($accountId, $resourceUid);
        if($accountResource instanceof AccountResource) {
            $accountResource->quantity += $amount;
            try {
                $this->update($accountResource);
            } catch (AccountResourceCouldNotBeUpdatedException $e) {
                throw new ResourceCouldNotBeAddedToInventoryException();
            }
            return;
        }

        $accountResource = new AccountResource();
        $accountResource->account = $accountId;
        $accountResource->resource = $resourceUid;
        $accountResource->quantity = $amount;

        if(false === $this->accountResourceTable->insert($accountResource))
        {
            throw new ResourceCouldNotBeAddedToInventoryException();
        }
    }

    public function takeFromInventory(int $accountId, string $resourceUid, int $amount): void
    {
        if($this->accountService->getAccountById($accountId) === null)
        {
            throw new AccountNotFoundException();
        }

        if($this->resourceService->getResourceByUid($resourceUid) === null)
        {
            throw new ResourceDoesNotExistException();
        }

        $accountResource = $this->getAccountInventoryItemAmount($accountId, $resourceUid);
        if(!$accountResource instanceof AccountResource) {
            throw new AccountResourceIsNotInInventoryException();
        }

        $accountResource->quantity -= $amount;
        if($accountResource->quantity < 0) {
            throw new AccountResourceAmountCantBeLessThanZeroException();
        }

        if($accountResource->quantity === 0)
        {
            if(false ===$this->accountResourceTable->delete($accountResource))
            {
                throw new ResourceCouldNotBeTakenFromInventoryException();
            }
        }

        try {
            $this->update($accountResource);
        } catch (AccountResourceCouldNotBeUpdatedException $e) {
            throw new ResourceCouldNotBeTakenFromInventoryException();
        }
    }

    public function getAccountInventory(int $accountId): array
    {
        if($this->accountService->getAccountById($accountId) === null)
        {
            throw new AccountNotFoundException();
        }

        $inventory = [];
        foreach ($this->accountResourceTable->findAllByAccount($accountId) as $inventoryItem) {
            $inventory[$inventoryItem->resource] = $inventoryItem;
        }

        return $inventory;
    }

    public function getAccountInventoryItemAmount(int $accountId, string $resourceUid): ?AccountResource
    {
        if($this->accountService->getAccountById($accountId) === null)
        {
            throw new AccountNotFoundException();
        }

        if($this->resourceService->getResourceByUid($resourceUid) === null)
        {
            throw new ResourceDoesNotExistException();
        }

        return $this->accountResourceTable->findByAccountIdAndResourceUid($accountId, $resourceUid);
    }

}
