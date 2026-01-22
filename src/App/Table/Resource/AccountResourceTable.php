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

    public function findByAccountIdAndResourceUid(UuidInterface $accountId, string $itemUid): ?array
    {
        $inventory = [];
        $query = new QueryBuilder($this->query);
        $queryResult = $query->from($this->getTableName())
            ->select('*')
            ->where('account = ? AND resource = ?')
            ->setParameter(0, $accountId->toString())
            ->setParameter(1, $itemUid)
            ->executeQuery()->fetchAllAssociative();

        if($queryResult)
        {
            foreach ($queryResult as $row)
            {
                $inventory[] = AccountResource::hydrate($row);
            }
        };
        return $inventory;
    }

    public function findAllByAccount(UuidInterface $account): ?array
    {
        $inventory = [];
        $query = new QueryBuilder($this->query);
        $queryResult = $query->from($this->getTableName())
            ->select('*')
            ->where('account = ?')
            ->setParameter(0, $account->toString())
            ->executeQuery()->fetchAllAssociative();

        if($queryResult) {
            foreach ($queryResult as $row) {
                $inventory[] = AccountResource::hydrate($row);
            }
        }
        return $inventory;
    }

}
