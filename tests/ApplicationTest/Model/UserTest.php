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

        $admin = new User(User::ROLE_ADMINISTRATOR);
        User::setCurrent($admin);
        self::assertSame($admin, User::getCurrent());
        $actual2 = $user2->getGlobalPermissions();
        self::assertSame($expected2, $actual2);
        self::assertSame($admin, User::getCurrent());
    }

    /**
     * @dataProvider providerSetRole
     *
     * @param string $currentRole
     * @param string $oldRole
     * @param string $newRole
     * @param null|string $exception
     */
    public function testSetRole(string $currentRole, string $oldRole, string $newRole, ?string $exception): void
    {
        if ($currentRole !== User::ROLE_ANONYMOUS) {
            $currentUser = new User($currentRole);
            User::setCurrent($currentUser);
        }

        $user2 = new User($oldRole);

        if ($exception) {
            $this->expectExceptionMessage($exception);
        }

        $user2->setRole($newRole);
        self::assertSame($newRole, $user2->getRole());
    }

    public function providerSetRole(): array
    {
        return [
            [User::ROLE_ANONYMOUS, User::ROLE_STUDENT, User::ROLE_JUNIOR, 'anonymous is not allowed to change role to junior'],

            [User::ROLE_STUDENT, User::ROLE_STUDENT, User::ROLE_STUDENT, null],
            [User::ROLE_STUDENT, User::ROLE_STUDENT, User::ROLE_JUNIOR, 'student is not allowed to change role to junior'],

            [User::ROLE_JUNIOR, User::ROLE_STUDENT, User::ROLE_JUNIOR, null],
            [User::ROLE_JUNIOR, User::ROLE_JUNIOR, User::ROLE_JUNIOR, null],
            'cannot promote higher than us' => [User::ROLE_JUNIOR, User::ROLE_JUNIOR, User::ROLE_SENIOR, 'junior is not allowed to change role to senior'],
            'cannot demote' => [User::ROLE_JUNIOR, User::ROLE_SENIOR, User::ROLE_JUNIOR, 'junior is not allowed to change role to junior'],

            [User::ROLE_ADMINISTRATOR, User::ROLE_JUNIOR, User::ROLE_SENIOR, null],
            [User::ROLE_ADMINISTRATOR, User::ROLE_JUNIOR, User::ROLE_ADMINISTRATOR, null],
            [User::ROLE_ADMINISTRATOR, User::ROLE_ADMINISTRATOR, User::ROLE_STUDENT, null],
        ];
    }
}
