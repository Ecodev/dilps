<?php

declare(strict_types=1);

namespace Application\Api;

use Doctrine\ORM\EntityManager;
use GraphQL\Doctrine\Types;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceManager;

class TypesFactory
{
    public function __invoke(ContainerInterface $container): Types
    {
        $entityManager = $container->get(EntityManager::class);

        $invokables = [
            \Application\Api\Enum\CardVisibilityType::class,
            \Application\Api\Enum\ChangeTypeType::class,
            \Application\Api\Enum\CollectionVisibilityType::class,
            \Application\Api\Enum\OrderType::class,
            \Application\Api\Enum\UserRoleType::class,
            \Application\Api\Enum\UserTypeType::class,
            \Application\Api\Input\Filter\OldArtistFilterType::class,
            \Application\Api\Input\Filter\OldCardFilterType::class,
            \Application\Api\Input\Filter\OldCollectionFilterType::class,
            \Application\Api\Input\Filter\OldInstitutionFilterType::class,
            \Application\Api\Input\Filter\OldUserFilterType::class,
            \Application\Api\Input\PaginationInputType::class,
            \Application\Api\MutationType::class,
            \Application\Api\Output\GlobalPermissionsListType::class,
            \Application\Api\Output\GlobalPermissionsType::class,
            \Application\Api\Output\PermissionsType::class,
            \Application\Api\QueryType::class,
            \Application\Api\Scalar\DateTimeType::class,
            \Application\Api\Scalar\LoginType::class,
            \GraphQL\Upload\UploadType::class,
        ];

        $aliases = [
            \DateTimeImmutable::class => \Application\Api\Scalar\DateTimeType::class,
            'datetime_immutable' => \Application\Api\Scalar\DateTimeType::class,
        ];

        // Automatically add aliases for GraphQL type name from the invokable types
        foreach ($invokables as $type) {
            $parts = explode('\\', $type);
            $alias = preg_replace('~Type$~', '', end($parts));
            $aliases[$alias] = $type;
        }

        $customTypes = new ServiceManager([
            'invokables' => $invokables,
            'aliases' => $aliases,
            'services' => [
//                // This is not quite right because it allow to compare a string with a json array.
//                // TODO: either hide the json_array filter or find a cleaner solution
//                'json_array' => GraphQL\Type\Definition\Type::string(),
            ],
            'abstract_factories' => [
                \Application\Api\Output\PaginationTypeFactory::class,
            ],
        ]);

        $types = new Types($entityManager, $customTypes);

        return $types;
    }
}
