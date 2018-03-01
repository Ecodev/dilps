<?php

declare(strict_types=1);

namespace Application\Action;

use Application\Model\Card;
use Application\Service\ImageService;
use Doctrine\ORM\EntityManager;
use Imagine\Image\ImagineInterface;
use Interop\Container\ContainerInterface;

class PptxFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $entityManager = $container->get(EntityManager::class);
        $imagine = $container->get(ImagineInterface::class);
        $imageService = $container->get(ImageService::class);

        return new PptxAction($entityManager->getRepository(Card::class), $imageService, $imagine);
    }
}
