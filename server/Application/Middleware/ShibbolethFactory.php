<?php

declare(strict_types=1);

namespace Application\Middleware;

use Interop\Container\ContainerInterface;

class ShibbolethFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return ShibbolethMiddleware
     */
    public function __invoke(ContainerInterface $container): ShibbolethMiddleware
    {
        return new ShibbolethMiddleware($container);
    }
}
