<?php
declare(strict_types=1);

namespace Merchant\Table;

use App\Table\AbstractTable;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Merchant\Model\MerchantAccountBought;

class MerchantAccountBoughtTable extends AbstractTable
{

    public function insert(MerchantAccountBought $merchantAccountBought): bool
    {
        $queryBuilder = new QueryBuilder($this->query);
        $queryResult = $queryBuilder->insert($this->getTableName());
        foreach ($merchantAccountBought as $column => $value) {
            $queryBuilder->setValue($column, ':' . $column);
            $queryBuilder->setParameter($column, $value);
        }

        try {
            $queryResult->executeQuery();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function update(MerchantAccountBought $merchantAccountBought): bool
    {
        $queryBuilder = new QueryBuilder($this->query);
        $queryResult = $queryBuilder->update($this->getTableName())
            ->where('merchant = :merchantId AND account = :accountId')
            ->setParameter('merchantId', $merchantAccountBought->merchant)
            ->setParameter('accountId', $merchantAccountBought->account);
        foreach ($merchantAccountBought as $column => $value) {
            $queryResult->set($column, ':' . $column);
            $queryResult->setParameter($column, $value);
        }

        try {
            $queryResult->executeQuery();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function findByMerchantAndAccount(int $merchantId, int $accountId): ?MerchantAccountBought
    {
        $queryBuilder = new QueryBuilder($this->query);
        try {
            $queryResult = $queryBuilder->from($this->getTableName())
                ->select('*')->where('merchant = :merchantId AND account = :accountId')
                ->setParameter('merchantId', $merchantId)
                ->setParameter('accountId', $accountId)
                ->fetchAssociative();

            if(false !== $queryResult)
            {
                return MerchantAccountBought::hydrate($queryResult);
            }
        } catch (Exception $e) {}
        return null;
    }

}
