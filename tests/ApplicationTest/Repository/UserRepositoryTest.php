<?php

declare(strict_types=1);

namespace ApplicationTest\Repository;

use Application\Model\User;
use Application\Repository\UserRepository;

/**
 * @group Repository
 */
class UserRepositoryTest extends AbstractRepositoryTest
{
    /**
     * @var UserRepository
     */
    private $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = _em()->getRepository(User::class);
    }

    public function testGetLoginPassword(): void
    {
        self::assertNull($this->repository->getLoginPassword('foo', 'bar'), 'wrong user');
        self::assertNull($this->repository->getLoginPassword('administrator', 'bar'), 'wrong password');

        $user = $this->repository->getLoginPassword('administrator', 'administrator');
        self::assertNotNull($user);
        self::assertSame(1000, $user->getId());

        $hash = _em()->getConnection()->query('SELECT password FROM `user` WHERE id = 1000')->fetchColumn();
        self::assertStringStartsWith('$', $hash, 'password should have been re-hashed automatically');
        self::assertNotSame(md5('administrator'), $hash, 'password should have been re-hashed automatically');
    }
}
