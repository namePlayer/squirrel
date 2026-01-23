<?php
declare(strict_types=1);

namespace App\Table\Resource;

use App\Model\AccountResource;
use App\Table\AbstractTable;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;

class AccountResourceTable extends AbstractTable
{

    public function insert(AccountResource $accountResource): bool
    {
        $queryBuilder = new QueryBuilder($this->query);
        $queryResult = $queryBuilder->insert($this->getTableName());
        $index = 0;
        foreach ($accountResource->extract() as $column => $value) {
            $queryResult->setValue($column, '?');
            $queryResult->setParameter($index, $value);
            $index++;
        }
        try {
            $queryResult->executeQuery();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function update(AccountResource $accountResource): bool
    {
        $queryBuilder = new QueryBuilder($this->query);
        $queryResult = $queryBuilder->update($this->getTableName())
            ->set('quantity', ':quantity')
            ->where('account = :accountId', 'resource = :resourceUid')
            ->setParameter('quantity', $accountResource->quantity)
            ->setParameter('accountId', $accountResource->account)
            ->setParameter('resourceUid', $accountResource->resource);
        try {
            $queryResult->executeQuery();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function delete(AccountResource $accountResource): bool
    {
        $queryBuilder = new QueryBuilder($this->query);
        $queryResult = $queryBuilder->delete($this->getTableName())
            ->where('account = :accountId', 'resource = :resourceUid')
            ->setParameter('accountId', $accountResource->account)
            ->setParameter('resourceUid', $accountResource->resource);
        try {
            $queryResult->executeQuery();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

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
