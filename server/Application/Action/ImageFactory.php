<?php

declare(strict_types=1);

namespace Application\Action;

use Application\Model\Card;
use Application\Service\ImageService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;

class ImageFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $entityManager = $container->get(EntityManager::class);
        $imageService = $container->get(ImageService::class);

        return new ImageAction($entityManager->getRepository(Card::class), $imageService);
    }
}
