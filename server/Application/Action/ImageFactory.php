<?php

declare(strict_types=1);

namespace Application\Action;

use Application\Model\Image;
use Doctrine\ORM\EntityManager;
use Imagine\Image\ImagineInterface;
use Interop\Container\ContainerInterface;

class ImageFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $entityManager = $container->get(EntityManager::class);
        $imagine = $container->get(ImagineInterface::class);

        return new ImageAction($entityManager->getRepository(Image::class), $imagine);
    }
}
