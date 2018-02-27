<?php

declare(strict_types=1);

namespace ApplicationTest\Acl;

use Application\Acl\Acl;
use Application\Model\Card;
use Application\Model\User;
use PHPUnit\Framework\TestCase;

class AclTest extends TestCase
{
    public function testIsCurrentUserAllowed(): void
    {
        $acl = new Acl();

        $user1 = new User();
        $card = new Card();

        User::setCurrent($user1);
        $card->timestampCreation();

        User::setCurrent(null);
        self::assertFalse($acl->isCurrentUserAllowed($card, 'update'), 'anonymous cannot update');
        self::assertSame('Non-logged user with role anonymous is not allowed on resource "Card#" with privilege "update"', $acl->getLastDenialMessage());

        User::setCurrent($user1);
        self::assertTrue($acl->isCurrentUserAllowed($card, 'update'), 'only owner can update');
        self::assertNull($acl->getLastDenialMessage());

        $user2 = new User();
        $user2->setLogin('John');
        User::setCurrent($user2);
        self::assertFalse($acl->isCurrentUserAllowed($card, 'update'), 'other user cannot update');
        self::assertSame('User "John" with role student is not allowed on resource "Card#" with privilege "update"', $acl->getLastDenialMessage());

        $user3 = new User();
        $user3->setRole(User::ROLE_ADMINISTRATOR);
        User::setCurrent($user3);
        self::assertTrue($acl->isCurrentUserAllowed($card, 'update'), 'admin can do anything');
        self::assertNull($acl->getLastDenialMessage());
    }
}
