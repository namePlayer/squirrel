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


    public function getAccountInventoryItemAmount(UuidInterface $accountId, string $resourceUid): ?AccountResource
    {
        $resources = $this->accountResourceTable->findByAccountIdAndResourceUid($accountId, $resourceUid);
        if(empty($resources)){
            return null;
        }
        $resourceOutput = $resources[0];

        foreach ($resources as $resource) {
            /* @var AccountResource $resource */
            if($resource->id === $resourceOutput->id){
                continue;
            }
            $resourceOutput->quantity += $resource->quantity;
        }
        return $resourceOutput;
    }

    public function getAccountInventory(UuidInterface $accountId): array
    {
        $inventory = [];
        foreach ($this->accountResourceTable->findAllByAccount($accountId) as $inventoryItem) {
            /* @var AccountResource $inventoryItem */
            if(isset($inventory[$inventoryItem->resource]))
            {
                $inventory[$inventoryItem->resource]->quantity += $inventoryItem->quantity;
                continue;
            }

            $inventory[$inventoryItem->resource] = $inventoryItem;
        }
        return $inventory;
    }

}
