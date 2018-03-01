<?php

declare(strict_types=1);

namespace Application\Service;

use Imagine\Image\ImagineInterface;
use Interop\Container\ContainerInterface;

class ImageFactory
{
    /**
     * Return the image service to be used to resize images
     *
     * @param ContainerInterface $container
     *
     * @return ImageService
     */
    public function __invoke(ContainerInterface $container): ImageService
    {
        $imagine = $container->get(ImagineInterface::class);

        return new ImageService($imagine);
    }
}
