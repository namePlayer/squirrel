<?php declare(strict_types=1);

namespace App\Table;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use ReflectionClass;

class AbstractTable
{

    private string $table;

    public function __construct(public readonly Connection $query)
    {
        $this->table = substr(new ReflectionClass($this)->getShortName(), 0, -5);
    }

    public function getTableName(): string
    {
        return $this->table;
    }

    public function findAll(): bool|array
    {
        $queryBuilder = new QueryBuilder($this->query);
        return $queryBuilder->from($this->table)
            ->fetchAllAssociative();
    }

}
