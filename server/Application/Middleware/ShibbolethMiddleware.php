<?php

declare(strict_types=1);

namespace Application\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\RedirectResponse;

class ShibbolethMiddleware implements MiddlewareInterface
{
    public function __construct()
    {
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Redirect to quizz url if quizz parameter is found in the query params
        $quizzUrl = array_key_exists('quizz', $request->getQueryParams()) ?
            'quizz;cards=' . $request->getQueryParams()['quizz'] . ';nav=0' : '';

        return new RedirectResponse('/' . $quizzUrl, 302);
    }
}
