<?php
declare(strict_types=1);

namespace App\Factory;

use App\Software;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

class OrmFactory
{

    public function create(Connection $connection): EntityManager
    {
        $config = ORMSetup::createAttributeMetadataConfig(
            paths: [Software::MODEL_DIR], isDevMode: true
        );
        $config->setProxyDir(Software::CACHE_DIR.'/orm');
        $config->setProxyNamespace('OrmProxy');

        return new EntityManager($connection, $config);
    }

}
