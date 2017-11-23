<?php

namespace App\Action;

use App\Model\User;
use Doctrine\ORM\EntityManager;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

class GraphQLAction implements MiddlewareInterface
{
    private $entityManger;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManger = $entityManager;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $users = $this->entityManger->getRepository(User::class)->findAll();

        $data = [];
        foreach ($users as $user) {
            $data[] = $user->getId() . ' ' . $user->getFirstname();
        }

        return new JsonResponse(['mes users' => $data]);
    }
}
