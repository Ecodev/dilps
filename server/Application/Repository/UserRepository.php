<?php

declare(strict_types=1);

namespace Application\Repository;

use Application\Model\User;
use DateTimeImmutable;
use Doctrine\ORM\QueryBuilder;

class UserRepository extends AbstractRepository
{
    public function getFindAllQuery(array $filters = []): QueryBuilder
    {
        $qb = $this->createQueryBuilder('user');

        if (@$filters['login']) {
            $qb->andWhere('user.login LIKE :login');
            $qb->setParameter('login', '%' . $filters['login'] . '%');
        }

        if (@$filters['email']) {
            $qb->andWhere('user.email LIKE :email');
            $qb->setParameter('email', '%' . $filters['email'] . '%');
        }

        if (@$filters['search']) {
            $qb->andWhere('user.login LIKE :search OR user.email LIKE :search');
            $qb->setParameter('search', '%' . $filters['search'] . '%');
        }

        return $qb;
    }

    /**
     * Returns the user authenticated by its login and password
     *
     * @param string $login
     * @param string $password
     *
     * @return null|User
     */
    public function getLoginPassword(string $login, string $password): ?User
    {
        /** @var User $user */
        $user = $this->findOneByLogin($login);

        if (!$user || ($user->getActiveUntil() && $user->getActiveUntil() < new DateTimeImmutable())) {
            return null;
        }

        $hashFromDb = $user->getPassword();
        $isMd5 = mb_strlen($hashFromDb) === 32 && ctype_xdigit($hashFromDb);

        // If we found a user and he has a correct MD5 or correct new hash, then return the user
        if (($isMd5 && md5($password) === $hashFromDb) || password_verify($password, $hashFromDb)) {

            // Update the hash in DB, if we are still MD5, or if PHP default options changed
            if ($isMd5 || password_needs_rehash($hashFromDb, PASSWORD_DEFAULT)) {
                $user->setPassword($password);
                _em()->flush();
            }

            return $user;
        }

        return null;
    }
}
