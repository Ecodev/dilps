<?php

declare(strict_types=1);

namespace Application\Acl\Assertion;

use Application\Model\User;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

class IsCreator implements AssertionInterface
{
    /**
     * Assert that the object has been created by the current user
     *
     * @param Acl $acl
     * @param RoleInterface $role
     * @param ResourceInterface $resource
     * @param string $privilege
     *
     * @return bool
     */
    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null)
    {
        $object = $resource->getInstance();

        return User::getCurrent() && User::getCurrent() === $object->getCreator();
    }
}
