<?php

declare(strict_types=1);

namespace Application\Api;

use GraphQL\Doctrine\DefaultFieldResolver;
use GraphQL\Error\Debug;
use GraphQL\Executor\ExecutionResult;
use GraphQL\GraphQL;
use GraphQL\Server\StandardServer;
use Psr\Http\Message\ServerRequestInterface;

/**
 * A thin wrapper to serve GraphQL via HTTP or CLI
 */
class Server
{
    private $server;

    public function __construct(bool $debug)
    {
        GraphQL::setDefaultFieldResolver(new DefaultFieldResolver());

        $this->server = new StandardServer([
            'schema' => new Schema(),
            'queryBatching' => true,
            'debug' => $debug ? Debug::INCLUDE_DEBUG_MESSAGE | Debug::INCLUDE_TRACE : false,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ExecutionResult
     */
    public function execute(ServerRequestInterface $request): ExecutionResult
    {
        if (!$request->getParsedBody()) {
            $request = $request->withParsedBody(json_decode($request->getBody()->getContents(), true));
        }

        return $this->server->executePsrRequest($request);
    }

    /**
     * Send response using standard PHP `header()` and `echo`.
     *
     * @param ExecutionResult|ExecutionResult[] $result
     */
    public function sendHttp($result): void
    {
        $this->server->getHelper()->sendResponse($result);
    }

    /**
     * Send response to CLI
     *
     * @param ExecutionResult $result
     */
    public function sendCli(ExecutionResult $result): void
    {
        echo json_encode($result, JSON_PRETTY_PRINT) . PHP_EOL;
    }
}
