<?php

declare(strict_types=1);

namespace Application\ORM;

use Application\ORM\Query\Filter\AclFilter;
use Psr\Container\ContainerInterface;

class EntityManagerFactory extends \ContainerInteropDoctrine\EntityManagerFactory
{
    /**
     * Return the preferred driver available on this system
     *
     * @param ContainerInterface $container
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function __invoke(ContainerInterface $container)
    {
        $entityManger = parent::__invoke($container);

        $entityManger->getFilters()->enable(AclFilter::class);

        return $entityManger;
    }
}
