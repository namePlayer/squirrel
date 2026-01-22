<?php
declare(strict_types=1);

namespace App\Table\Account;

use App\Model\Account;
use App\Table\AbstractTable;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;

class AccountTable extends AbstractTable
{

    public function insert(Account $account): bool
    {
        $queryBuilder = new QueryBuilder($this->query);
        $queryResult = $queryBuilder->insert($this->getTableName());
        $index = 0;
        foreach ($account->extract(ignoreId: true) as $field => $value) {
            $queryResult->setValue($field, '?');
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

    public function findById(int $id): ?Account
    {
        $queryBuilder = new QueryBuilder($this->query);
        $queryResult = $queryBuilder->from($this->getTableName())
            ->select("*")
            ->where('id = ?')
            ->setParameter(0, $id)
            ->fetchAssociative();
        if(false !== $queryResult) {
            return Account::hydrate($queryResult);
        }
        return null;
    }

    public function findBySlug(string $slug): ?Account
    {
        $queryBuilder = new QueryBuilder($this->query);
        $queryResult = $queryBuilder->from($this->getTableName())
            ->select("*")
            ->where('slug = ?')
            ->setParameter(0, $slug)
            ->fetchAssociative();
        if(false !== $queryResult) {
            return Account::hydrate($queryResult);
        }
        return null;
    }

    public function findByEmail(string $email): ?Account
    {
        $queryBuilder = new QueryBuilder($this->query);
        $queryResult = $queryBuilder->from($this->getTableName())
            ->select("*")
            ->where('email = ?')
            ->setParameter(0, $email)
            ->fetchAssociative();
        if(false !== $queryResult) {
            return Account::hydrate($queryResult);
        }
        return null;
    }

}
