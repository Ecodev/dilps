<?php

declare(strict_types=1);

namespace Application\Service;

use Imagine\Image\ImagineInterface;

class ImagineFactory
{
    /**
     * Return the preferred driver available on this system
     *
     * @return ImagineInterface
     */
    public function __invoke(): ImagineInterface
    {
        if (class_exists('Gmagick')) {
            return new \Imagine\Gmagick\Imagine();
        }

        if (class_exists('Imagick')) {
            return new \Imagine\Imagick\Imagine();
        }

        return new \Imagine\Gd\Imagine();
    }
}
