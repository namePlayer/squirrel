<?php
declare(strict_types=1);

namespace App\Table\Account;

use App\Model\Account;
use App\Table\AbstractTable;
use Doctrine\DBAL\Exception;

class AccountTable extends AbstractTable
{

    public function insert(Account $account): bool
    {
        $query = $this->query->insert($this->getTableName());
        $index = 0;
        foreach ($account->extract() as $field => $value) {
            $query->setValue($field, '?');
            $query->setParameter($index, $value);
            $index++;
        }
        try {
            $query->executeQuery();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function findById(string $id): ?Account
    {
        $queryResult = $this->query->from($this->getTableName())
            ->select("*")
            ->where('id = ?')
            ->setParameter(0, $id)
            ->fetchAssociative();
        if(false !== $queryResult) {
            return Account::hydrate($queryResult);
        }
        return null;
    }

    public function findByEmail(string $email): ?Account
    {
        $queryResult = $this->query->from($this->getTableName())
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
