<?php

declare(strict_types=1);

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Tools\Console\Helper\ConfigurationHelper;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Symfony\Component\Console\Helper\HelperSet;

$container = require 'config/container.php';
$entityManager = $container->get(EntityManager::class);

// Configure migrations
$connection = $entityManager->getConnection();
$configuration = new Configuration($connection);
$configuration->setMigrationsDirectory('server/Application/Migration');
$configuration->setMigrationsNamespace('Application\Migration');
$configuration->setCustomTemplate('config/migration-template.txt');

return new HelperSet([
    'em' => new EntityManagerHelper($entityManager),
    'db' => new ConnectionHelper($entityManager->getConnection()),
    'configuration' => new ConfigurationHelper($entityManager->getConnection(), $configuration),
]);
