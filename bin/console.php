<?php declare(strict_types=1);

use Symfony\Component\Console\Application;

require_once __DIR__.'/../vendor/autoload.php';

App\Software::initEnvironment();

require_once __DIR__.'/../config/container.php';

/* @var \League\Container\Container $container */

$console = new Application();

$console->addCommand(
    new \App\Command\Account\AppAccountCreateCommand(
        $container->get(\App\Service\Account\AccountService::class),
        $container->get(\App\Service\Account\PasswordService::class),
        $container->get(\App\Validator\AccountRegistrationValidator::class),
    )
);

$console->addCommand(new \App\Command\Resource\AppResourceSyncCommand(
    $container->get(\App\Service\Resource\ResourceService::class),
));

$console->addCommand(new \App\Command\Resource\AppResourceGetCommand(
    $container->get(\App\Service\Resource\ResourceService::class),
));

$console->addCommand(new \App\Command\Inventory\AppInventoryAddCommand(
    $container->get(\App\Service\Economy\InventoryService::class),
));

$console->addCommand(new \App\Command\Inventory\AppInventoryTakeCommand(
    $container->get(\App\Service\Economy\InventoryService::class),
));


$console->addCommand(new \App\Command\Inventory\AppInventoryViewCommand(
    $container->get(\App\Service\Economy\InventoryService::class),
    $container->get(\App\Service\Account\AccountService::class)
));

$console->addCommand(new \App\Command\Money\AppMoneyAddCommand(
    $container->get(\App\Service\Economy\MoneyService::class),
));

$console->addCommand(new \App\Command\Money\AppMoneyTakeCommand(
    $container->get(\App\Service\Economy\MoneyService::class),
));

$console->addCommand(new \App\Command\Money\AppMoneyViewCommand(
    $container->get(\App\Service\Economy\MoneyService::class),
));

$console->addCommand(new \Merchant\Command\MerchantOfferCreateCommand(
    $container->get(\Merchant\Service\MerchantOfferService::class)
));

$console->addCommand(new \Merchant\Command\MerchantOfferListCommand(
    $container->get(\Merchant\Service\MerchantOfferService::class)
));

$console->run();
