<?php

declare(strict_types=1);

namespace Application\Api\Enum;

use Application\Model\Image;

class ImageTypeType extends AbstractEnumType
{
    public function __construct()
    {
        $config = [
            Image::TYPE_DEFAULT => 'Default',
            Image::TYPE_IMAGE => 'An image',
            Image::TYPE_ARCHITECTURE => 'Architecture',
        ];

        parent::__construct($config);
    }
}
