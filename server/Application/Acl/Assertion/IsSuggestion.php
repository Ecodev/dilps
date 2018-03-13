<?php

declare(strict_types=1);

namespace Application\Acl\Assertion;

use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

class IsSuggestion implements AssertionInterface
{
    /**
     * Assert that the card is a suggestion (has an original)
     *
     * @param  Acl $acl
     * @param  RoleInterface $role
     * @param  ResourceInterface $resource
     * @param  string $privilege
     *
     * @return bool
     */
    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null)
    {
        $object = $resource->getInstance();
        $isOwner = new IsOwner();

        return (bool) $object->getOriginal() && $isOwner->assert($acl, $role, $resource, $privilege);
    }
}
