<?php

declare(strict_types=1);
use Doctrine\ORM\EntityManager;
use GraphQL\Doctrine\Types;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;

// Load configuration
$config = require __DIR__ . '/config.php';

// Build container
global $container;
$container = new ServiceManager();
(new Config($config['dependencies']))->configureServiceManager($container);

// Inject config
$container->setService('config', $config);

/**
 * Returns the type registry
 *
 * @return Types
 */
function _types(): Types
{
    global $container;

    return $container->get(Types::class);
}

/**
 * Returns the EM
 *
 * @return EntityManager
 */
function _em(): EntityManager
{
    global $container;

    return $container->get(EntityManager::class);
}

return $container;
