<?php
declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

\App\Software::initEnvironment();

require_once __DIR__.'/../config/container.php';
/* @var \League\Container\Container $container */

\Doctrine\ORM\Tools\Console\ConsoleRunner::run(
    new \Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider($container->get(\Doctrine\ORM\EntityManager::class))
);
