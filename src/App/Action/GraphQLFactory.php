<?php

namespace App\Action;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class GraphQLFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $entityManager = $container->get('doctrine.entity_manager.orm_default');

        return new GraphQLAction($entityManager);
    }
}
