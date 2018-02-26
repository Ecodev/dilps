<?php

declare(strict_types=1);

namespace Application\Middleware;

use Application\Model\User;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;

class AuthenticationFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $entityManager = $container->get(EntityManager::class);

        return new AuthenticationMiddleware($entityManager->getRepository(User::class));
    }
}
