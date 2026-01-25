<?php
declare(strict_types=1);

namespace Merchant\Table;

use App\Table\AbstractTable;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Merchant\Model\Merchant;

class MerchantTable extends AbstractTable
{

    public function insert(Merchant $merchant): bool
    {
        $queryBuilder = new QueryBuilder($this->query);
        $queryResult = $queryBuilder->insert($this->getTableName());
        foreach ($merchant->extract(ignoreId: true) as $column => $value) {
            $queryResult->setValue($column, ':'.$column);
            $queryResult->setParameter($column, $value);
        }
        try {
            $queryResult->executeQuery();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function findAllByExpiresGreaterThan(\DateTime $expires): array
    {
        $queryBuilder = new QueryBuilder($this->query);
        $queryResult = $queryBuilder->select('*')
            ->from($this->getTableName())
            ->where('expires > :expires')
            ->setParameter('expires', $expires->format('Y-m-d H:i:s'));
        $offers = [];
        try {
            foreach ($queryResult->fetchAllAssociative() as $offer) {
                $offer = Merchant::hydrate($offer);
                $offers[$offer->id] = $offer;
            }
        } catch (Exception $e) {}
        return $offers;
    }

    public function findBySlug(string $slug): ?Merchant
    {
        $queryBuilder = new QueryBuilder($this->query);
        $queryResult = $queryBuilder->select('*')
            ->from($this->getTableName())
            ->where('slug = :slug')
            ->setParameter('slug', $slug);

        try {
            $result = $queryResult->fetchAssociative();
            if(false !== $result)
            {
                return Merchant::hydrate($result);
            }
        } catch (Exception $e) {
        }
        return null;
    }

}
