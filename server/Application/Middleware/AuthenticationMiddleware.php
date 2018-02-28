<?php

declare(strict_types=1);

namespace Application\Middleware;

use Application\Model\User;
use Application\Repository\UserRepository;
use DateTimeImmutable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Session\SessionInterface;
use Zend\Expressive\Session\SessionMiddleware;

class AuthenticationMiddleware implements MiddlewareInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Load current user from session if exists and still valid
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var SessionInterface $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);

        if ($session->has('user')) {
            $user = $this->userRepository->getOneById($session->get('user'));

            if ($user && (!$user->getActiveUntil() || $user->getActiveUntil() > new DateTimeImmutable())) {
                User::setCurrent($user);
            }
        }

        if (!User::getCurrent()) {
            $session->clear();
        }

        return $handler->handle($request);
    }
}
