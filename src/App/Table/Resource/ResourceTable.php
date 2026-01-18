<?php
declare(strict_types=1);

namespace App\Table\Resource;

use App\Model\Resource;
use App\Table\AbstractTable;
use Doctrine\DBAL\Exception;

class ResourceTable extends AbstractTable
{

    public function insert(Resource $resource): bool
    {
        $query = $this->query->insert($this->getTableName());
        $index = 0;
        foreach ($resource->extract() as $field => $value) {
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

    public function findByUid(string $uid): ?Resource
    {
        $queryResult = $this->query->from($this->getTableName())
            ->select('uid')
            ->where('uid = ?')
            ->setParameter(0, $uid)
            ->fetchAssociative();
        if(false !== $queryResult) {
            return Resource::hydrate($queryResult);
        }
        return null;
    }

}
