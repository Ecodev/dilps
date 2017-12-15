<?php

declare(strict_types=1);

namespace Application\Api;

use Application\Model\AbstractModel;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use GraphQL\Doctrine\Definition\EntityID;

abstract class Helper
{
    public static function paginate(array $pagination, QueryBuilder $query): array
    {
        $pageIndex = $pagination['pageIndex'];
        $pageSize = $pagination['pageSize'];

        $paginator = new Paginator($query);
        $paginator
            ->getQuery()
            ->setFirstResult($pageSize * $pageIndex)
            ->setMaxResults($pageSize);

        $pagination['length'] = $paginator->count();
        $pagination['items'] = $paginator->getIterator();

        return $pagination;
    }

    public static function hydrate(AbstractModel $object, array $input): void
    {
        foreach ($input as $name => $value) {
            if ($value instanceof EntityID) {
                $value = $value->getEntity();
            }

            $setter = 'set' . ucfirst($name);
            $object->$setter($value);
        }
    }
}
