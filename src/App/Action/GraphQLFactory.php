<?php

declare(strict_types=1);

namespace App\Action;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;

class GraphQLFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $entityManager = $container->get(EntityManager::class);

        return new GraphQLAction($entityManager);
    }
}
