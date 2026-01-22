<?php declare(strict_types=1);

use League\Container\Container;

$container = new Container();

#
# Controllers
#
$container->add(\App\Controller\IndexController::class)
    ->addArgument(\League\Plates\Engine::class);

#
# Services
#
$container->add(\App\Service\Account\AccountService::class)
    ->addArgument(\App\Table\Account\AccountTable::class)
    ->addArgument(\App\Service\Account\PasswordService::class);

$container->add(\App\Service\Account\PasswordService::class);

$container->add(\App\Service\Resource\ResourceService::class)
    ->addArgument(\App\Table\Resource\ResourceTable::class);

$container->add(\App\Service\Resource\InventoryService::class)
    ->addArgument(\App\Service\Resource\ResourceService::class)
    ->addArgument(\App\Table\Resource\AccountResourceTable::class);

#
# Repositories
#
$container->add(\App\Table\Account\AccountTable::class)
    ->addArgument(\Doctrine\DBAL\Connection::class);

$container->add(\App\Table\Resource\ResourceTable::class)
    ->addArgument(\Doctrine\DBAL\Connection::class);

$container->add(\App\Table\Resource\AccountResourceTable::class)
    ->addArgument(\Doctrine\DBAL\Connection::class);

#
# Validator
#
$container->add(\App\Validator\AccountRegistrationValidator::class);

#
# Dependencies
#

$container->add(\Doctrine\DBAL\Connection::class, new \App\Factory\DatabaseFactory()->connect());

$container->add(\Doctrine\DBAL\Query\QueryBuilder::class, new \App\Factory\DatabaseFactory()->queryBuilder());

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
