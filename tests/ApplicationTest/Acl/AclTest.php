<?php

declare(strict_types=1);

namespace ApplicationTest\Acl;

use Application\Acl\Acl;
use Application\Model\Card;
use Application\Model\Change;
use Application\Model\Collection;
use Application\Model\User;
use PHPUnit\Framework\TestCase;

class AclTest extends TestCase
{
    public function testIsCurrentUserAllowed(): void
    {
        $acl = new Acl();
        $card = new Card();

        $ownerStudent = new User();
        $ownerStudent->setLogin('Sarah');
        User::setCurrent($ownerStudent);
        $card->timestampCreation();

        User::setCurrent(null);
        self::assertFalse($acl->isCurrentUserAllowed($card, 'update'), 'anonymous cannot update');
        self::assertSame('Non-logged user with role anonymous is not allowed on resource "Card#" with privilege "update"', $acl->getLastDenialMessage());

        User::setCurrent($ownerStudent);
        self::assertFalse($acl->isCurrentUserAllowed($card, 'update'), 'student cannot update even if owner');
        self::assertSame('User "Sarah" with role student is not allowed on resource "Card#" with privilege "update"', $acl->getLastDenialMessage());

        $ownerJunior = new User(User::ROLE_JUNIOR);
        $ownerJunior->setLogin('Kyle');
        User::setCurrent($ownerJunior);
        $card->timestampCreation();

        self::assertTrue($acl->isCurrentUserAllowed($card, 'update'), 'only junior owner can update');
        self::assertNull($acl->getLastDenialMessage());
        self::assertTrue($acl->isCurrentUserAllowed($card, 'delete'), 'junior can delete his card');
        self::assertNull($acl->getLastDenialMessage());

        $change = new Change();
        $change->setSuggestion($card);
        self::assertFalse($acl->isCurrentUserAllowed($card, 'delete'), 'junior cannot delete his card if it is a suggestion');
        self::assertSame('User "Kyle" with role junior is not allowed on resource "Card#" with privilege "delete"', $acl->getLastDenialMessage());

        $otherStudent = new User();
        $otherStudent->setLogin('John');
        User::setCurrent($otherStudent);
        self::assertFalse($acl->isCurrentUserAllowed($card, 'update'), 'other user cannot update');
        self::assertSame('User "John" with role student is not allowed on resource "Card#" with privilege "update"', $acl->getLastDenialMessage());

        $administrator = new User(User::ROLE_ADMINISTRATOR);
        $administrator->setLogin('Jane');
        User::setCurrent($administrator);
        self::assertTrue($acl->isCurrentUserAllowed($card, 'update'), 'admin can do anything');
        self::assertNull($acl->getLastDenialMessage());

        $collection = new Collection();
        self::assertFalse($acl->isCurrentUserAllowed($collection, 'read'), 'admin cannot read non-admin collection');
        self::assertSame('User "Jane" with role administrator is not allowed on resource "Collection#" with privilege "read"', $acl->getLastDenialMessage());

        $collection->setVisibility(Collection::VISIBILITY_ADMINISTRATOR);
        self::assertTrue($acl->isCurrentUserAllowed($collection, 'read'), 'admin can do anything');
        self::assertNull($acl->getLastDenialMessage());
    }
}
