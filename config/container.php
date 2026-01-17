<?php declare(strict_types=1);

use League\Container\Container;

$container = new Container();

#
# Controllers
#
$container->add(\App\Controller\IndexController::class)
    ->addArgument(\League\Plates\Engine::class)
    ->addArgument(\Doctrine\DBAL\Query\QueryBuilder::class);

#
# Services
#

#
# Repositories
#

#
# Dependencies
#

$container->add(\Doctrine\DBAL\Connection::class, new \App\Factory\DatabaseFactory()->connect());

$container->add(\Doctrine\DBAL\Query\QueryBuilder::class, new \App\Factory\DatabaseFactory()->queryBuilder());

$container->add(\Doctrine\ORM\EntityManager::class, new \App\Factory\OrmFactory()->create(
    $container->get(\Doctrine\DBAL\Connection::class)
));

$container->add(\Monolog\Logger::class)
    ->addArgument('app')
    ->addMethodCall('pushHandler',
        [(new \App\Factory\LoggerFactory())->createPushHandler()]
    );

$container->add(League\Plates\Engine::class)
    ->addArgument(__DIR__.'/../template');

$responseFactory = (new \Laminas\Diactoros\ResponseFactory());
$jsonStrategy = (new \League\Route\Strategy\JsonStrategy($responseFactory))->setContainer($container);
$applicationStrategy = (new \League\Route\Strategy\ApplicationStrategy())->setContainer($container);
$router = (new \League\Route\Router())->setStrategy($applicationStrategy);
