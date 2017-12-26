<?php

declare(strict_types=1);

namespace Application\Action;

use Application\Api\Schema;
use Doctrine\ORM\EntityManager;
use GraphQL\Doctrine\DefaultFieldResolver;
use GraphQL\GraphQL;
use GraphQL\Server\StandardServer;
use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
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
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        GraphQL::setDefaultFieldResolver(new DefaultFieldResolver());

        $schema = new Schema();
        $server = new StandardServer([
            'schema' => $schema,
            'queryBatching' => true,
            'debug' => true,
        ]);

        $response = $server->executePsrRequest($request);

        return new JsonResponse($response);
    }
}
