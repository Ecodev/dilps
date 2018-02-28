<?php

declare(strict_types=1);

namespace Application\Api;

use Doctrine\Common\Persistence\Proxy;
use Doctrine\ORM\EntityNotFoundException;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * A field resolver that will ensure that filtered entity are never returned via getter
 */
class DefaultFieldResolver
{
    private $resolver;

    public function __construct()
    {
        $this->resolver = new \GraphQL\Doctrine\DefaultFieldResolver();
    }

    public function __invoke($source, $args, $context, ResolveInfo $info)
    {
        $value = $this->resolver->__invoke($source, $args, $context, $info);

        return $this->load($value);
    }

    /**
     * Try to load the entity from DB, but if it is filtered, it will return null.
     *
     * This mechanic is necessary to hide entities that should have been filtered by
     * AclFilter, but that are accessed via lazy-loaded by doctrine on a *-to-one relation.
     * This scenario is described in details on https://github.com/doctrine/doctrine2/issues/4543
     *
     * @param mixed $object or any kind of value
     *
     * @return mixed
     */
    private function load($object)
    {
        if ($object instanceof Proxy) {
            try {
                $object->__load();
            } catch (EntityNotFoundException $exception) {
                return null;
            }
        }

        return $object;
    }
}
