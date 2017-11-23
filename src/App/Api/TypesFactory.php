<?php

declare(strict_types=1);

namespace App\Api;

use App\Api\Scalar\DateTimeType;
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
        ];

        $types = new Types($entityManager, $mapping);

        return $types;
    }
}
