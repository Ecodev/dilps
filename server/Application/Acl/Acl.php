<?php

declare(strict_types=1);

namespace Application\Acl;

use Application\Acl\Assertion\IsMyself;
use Application\Acl\Assertion\IsOwner;
use Application\Acl\Assertion\IsSuggestion;
use Application\Acl\Assertion\Visibility;
use Application\Model\AbstractModel;
use Application\Model\Artist;
use Application\Model\Card;
use Application\Model\Change;
use Application\Model\Collection;
use Application\Model\Country;
use Application\Model\Dating;
use Application\Model\Institution;
use Application\Model\Tag;
use Application\Model\User;
use Doctrine\Common\Util\ClassUtils;

class Acl extends \Zend\Permissions\Acl\Acl
{
    /**
     * The message explaining the last denial
     *
     * @var null|string
     */
    private $message;

    public function __construct()
    {
        $this->addRole(User::ROLE_ANONYMOUS);
        $this->addRole(User::ROLE_STUDENT, User::ROLE_ANONYMOUS);
        $this->addRole(User::ROLE_JUNIOR, User::ROLE_STUDENT);
        $this->addRole(User::ROLE_SENIOR, User::ROLE_JUNIOR);
        $this->addRole(User::ROLE_ADMINISTRATOR);

        $this->addResource(new ModelResource(Artist::class));
        $this->addResource(new ModelResource(Card::class));
        $this->addResource(new ModelResource(Change::class));
        $this->addResource(new ModelResource(Collection::class));
        $this->addResource(new ModelResource(Country::class));
        $this->addResource(new ModelResource(Dating::class));
        $this->addResource(new ModelResource(Institution::class));
        $this->addResource(new ModelResource(Tag::class));
        $this->addResource(new ModelResource(User::class));

        $this->allow(User::ROLE_ANONYMOUS, new ModelResource(Artist::class), 'read');
        $this->allow(User::ROLE_ANONYMOUS, new ModelResource(Card::class), 'read', new Visibility(Card::VISIBILITY_PUBLIC));
        $this->allow(User::ROLE_ANONYMOUS, new ModelResource(Country::class), 'read');
        $this->allow(User::ROLE_ANONYMOUS, new ModelResource(Dating::class), 'read');
        $this->allow(User::ROLE_ANONYMOUS, new ModelResource(Institution::class), 'read');
        $this->allow(User::ROLE_ANONYMOUS, new ModelResource(Tag::class), 'read');

        $this->allow(User::ROLE_STUDENT, new ModelResource(Artist::class), 'create');
        $this->allow(User::ROLE_STUDENT, new ModelResource(Card::class), 'create');
        $this->allow(User::ROLE_STUDENT, new ModelResource(Card::class), ['update'], new IsSuggestion());
        $this->allow(User::ROLE_STUDENT, new ModelResource(Card::class), 'read', new Visibility(Card::VISIBILITY_MEMBER));
        $this->allow(User::ROLE_STUDENT, new ModelResource(Collection::class), 'read', new Visibility(Collection::VISIBILITY_MEMBER));
        $this->allow(User::ROLE_STUDENT, new ModelResource(Change::class), 'read', new IsOwner());
        $this->allow(User::ROLE_STUDENT, new ModelResource(Change::class), 'create');
        $this->allow(User::ROLE_STUDENT, new ModelResource(Collection::class), 'create');
        $this->allow(User::ROLE_STUDENT, new ModelResource(Collection::class), ['update', 'delete'], new IsOwner());
        $this->allow(User::ROLE_STUDENT, new ModelResource(Institution::class), 'create');
        $this->allow(User::ROLE_STUDENT, new ModelResource(Tag::class), 'create');
        $this->allow(User::ROLE_STUDENT, new ModelResource(User::class), 'read');
        $this->allow(User::ROLE_STUDENT, new ModelResource(User::class), ['update', 'delete'], new IsMyself());

        $this->allow(User::ROLE_JUNIOR, new ModelResource(Card::class), ['update'], new IsOwner());

        $this->allow(User::ROLE_SENIOR, new ModelResource(Card::class), ['delete'], new IsOwner());

        // Administrator inherits nothing, but is allowed almost all privileges
        $this->allow(User::ROLE_ADMINISTRATOR, new ModelResource(Artist::class));
        $this->allow(User::ROLE_ADMINISTRATOR, new ModelResource(Card::class));
        $this->allow(User::ROLE_ADMINISTRATOR, new ModelResource(Change::class));
        $this->allow(User::ROLE_ADMINISTRATOR, new ModelResource(Collection::class), 'create');
        $this->allow(User::ROLE_ADMINISTRATOR, new ModelResource(Collection::class), null, new Visibility(Collection::VISIBILITY_ADMINISTRATOR));
        $this->allow(User::ROLE_ADMINISTRATOR, new ModelResource(Country::class));
        $this->allow(User::ROLE_ADMINISTRATOR, new ModelResource(Dating::class));
        $this->allow(User::ROLE_ADMINISTRATOR, new ModelResource(Institution::class));
        $this->allow(User::ROLE_ADMINISTRATOR, new ModelResource(Tag::class));
        $this->allow(User::ROLE_ADMINISTRATOR, new ModelResource(User::class));
    }

    /**
     * Return whether the current user is allowed to do something
     *
     * This should be the main method to do all ACL checks.
     *
     * @param AbstractModel $model
     * @param string $privilege
     *
     * @return bool
     */
    public function isCurrentUserAllowed(AbstractModel $model, string $privilege): bool
    {
        $resource = new ModelResource($this->getClass($model), $model);

        $role = $this->getCurrentRole();

        $isAllowed = $this->isAllowed($role, $resource, $privilege);

        $this->message = $this->buildMessage($resource, $privilege, $role, $isAllowed);

        return $isAllowed;
    }

    private function getClass(AbstractModel $resource): string
    {
        return ClassUtils::getRealClass(get_class($resource));
    }

    private function getCurrentRole(): string
    {
        $user = User::getCurrent();
        if (!$user) {
            return 'anonymous';
        }

        return $user->getRole();
    }

    private function buildMessage($resource, ?string $privilege, string $role, bool $isAllowed): ?string
    {
        if ($isAllowed) {
            return null;
        }

        if ($resource instanceof ModelResource) {
            $resource = $resource->getName();
        }

        $user = User::getCurrent() ? 'User "' . User::getCurrent()->getLogin() . '"' : 'Non-logged user';
        $privilege = $privilege === null ? 'NULL' : $privilege;

        return "$user with role $role is not allowed on resource \"$resource\" with privilege \"$privilege\"";
    }

    /**
     * Returns the message explaining the last denial, if any
     *
     * @return null|string
     */
    public function getLastDenialMessage(): ?string
    {
        return $this->message;
    }
}
