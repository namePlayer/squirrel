<?php
declare(strict_types=1);

namespace App\Service\Resource;

use App\Model\AccountResource;
use App\Table\Resource\AccountResourceTable;
use Ramsey\Uuid\UuidInterface;

class InventoryService
{

    public function __construct(
        private readonly ResourceService $resourceService,
        private readonly AccountResourceTable $accountResourceTable
    )
    {
    }

    public function getAccountInventory(int $accountId): array
    {
        $inventory = [];
        foreach ($this->accountResourceTable->findAllByAccount($accountId) as $inventoryItem) {
            $inventory[$inventoryItem->resource] = $inventoryItem;
        }

        return $inventory;
    }

    public function getAccountInventoryItemAmount(int $accountId, string $resourceUid): ?AccountResource
    {
        return $this->accountResourceTable->findByAccountIdAndResourceUid($accountId, $resourceUid);
    }

}
