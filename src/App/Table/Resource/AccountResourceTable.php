<?php
declare(strict_types=1);

namespace App\Table\Resource;

use App\Model\Account;
use App\Model\AccountResource;
use App\Model\Resource;
use App\Table\AbstractTable;
use Doctrine\DBAL\Query\QueryBuilder;
use Ramsey\Uuid\UuidInterface;

class AccountResourceTable extends AbstractTable
{

    public function findByAccountIdAndResourceUid(int $accountId, string $itemUid): ?AccountResource
    {
        $query = new QueryBuilder($this->query);
        $queryResult = $query->from($this->getTableName())
            ->select('*')
            ->where('account = ? AND resource = ?')
            ->setParameter(0, $accountId)
            ->setParameter(1, $itemUid)
            ->fetchAssociative();
        if(false !== $queryResult) {
            return AccountResource::hydrate($queryResult);
        }
        return null;
    }

    public function findAllByAccount(int $accountId): ?array
    {
        $inventory = [];
        $query = new QueryBuilder($this->query);
        $queryResult = $query->from($this->getTableName())
            ->select('*')
            ->where('account = ?')
            ->setParameter(0, $accountId)
            ->executeQuery()->fetchAllAssociative();

        if($queryResult) {
            foreach ($queryResult as $row) {
                $inventory[] = AccountResource::hydrate($row);
            }
        }
        return $inventory;
    }

}
