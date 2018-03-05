<?php

declare(strict_types=1);

namespace ApplicationTests\Model;

use Application\Model\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function tearDown(): void
    {
        User::setCurrent(null);
    }

    public function testGetGlobalPermissions(): void
    {
        $user = new User();
        $actual = $user->getGlobalPermissions();
        $expected = [
            'artist' => [
                'create' => false,
            ],
            'card' => [
                'create' => false,
            ],
            'change' => [
                'create' => false,
            ],
            'collection' => [
                'create' => false,
            ],
            'country' => [
                'create' => false,
            ],
            'dating' => [
                'create' => false,
            ],
            'institution' => [
                'create' => false,
            ],
            'tag' => [
                'create' => false,
            ],
            'user' => [
                'create' => false,
            ],
        ];

        self::assertEquals($expected, $actual);

        $expected2 = [
            'artist' => [
                'create' => true,
            ],
            'card' => [
                'create' => true,
            ],
            'change' => [
                'create' => true,
            ],
            'collection' => [
                'create' => true,
            ],
            'country' => [
                'create' => true,
            ],
            'dating' => [
                'create' => true,
            ],
            'institution' => [
                'create' => true,
            ],
            'tag' => [
                'create' => true,
            ],
            'user' => [
                'create' => true,
            ],
        ];

        $user2 = new User();
        $user->setRole(User::ROLE_ADMINISTRATOR);

        User::setCurrent($user);
        self::assertSame($user, User::getCurrent());
        $actual2 = $user2->getGlobalPermissions();
        self::assertSame($expected2, $actual2);
        self::assertSame($user, User::getCurrent());
    }
}
