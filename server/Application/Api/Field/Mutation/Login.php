<?php

declare(strict_types=1);

namespace Application\Api\Field\Mutation;

use Application\Api\Exception;
use Application\Api\Field\FieldInterface;
use Application\Api\Scalar\LoginType;
use Application\Model\User;
use GraphQL\Type\Definition\Type;
use Zend\Expressive\Session\SessionInterface;

abstract class Login implements FieldInterface
{
    public static function build(): array
    {
        return [
            'name' => 'login',
            'type' => Type::nonNull(_types()->get(User::class)),
            'description' => 'Log in a user',
            'args' => [
                'login' => Type::nonNull(_types()->get(LoginType::class)),
                'password' => Type::nonNull(Type::string()),
            ],
            'resolve' => function ($root, array $args, SessionInterface $session): User {

                // Logout
                $session->clear();
                User::setCurrent(null);

                $user = _em()->getRepository(User::class)->getLoginPassword($args['login'], $args['password']);

                // If we successfully authenticated or we already were logged in, keep going
                if ($user) {
                    $session->set('user', $user->getId());
                    User::setCurrent($user);

                    return $user;
                }

                throw new Exception("Le nom d'utilisateur ou mot de passe est incorrect !");
            },
        ];
    }
}
