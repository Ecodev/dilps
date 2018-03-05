<?php

declare(strict_types=1);

namespace Application\Api;

use Application\Api\Output\GlobalPermissionsListType;
use Application\Api\Output\GlobalPermissionsType;
use Application\Api\Output\PermissionsType;
use Application\Api\Scalar\DateTimeType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use GraphQL\Doctrine\Types;
use Interop\Container\ContainerInterface;

class TypesFactory
{
    public function __invoke(ContainerInterface $container): Types
    {
        $entityManager = $container->get(EntityManager::class);

        $mapping = [
            DateTimeImmutable::class => DateTimeType::class,
            PermissionsType::class => PermissionsType::class,
            GlobalPermissionsType::class => GlobalPermissionsType::class,
            GlobalPermissionsListType::class => GlobalPermissionsListType::class,
        ];

        $types = new Types($entityManager, $mapping);

        return $types;
    }
}
