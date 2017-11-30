<?php

declare(strict_types=1);

namespace Application\Api\Enum;

use Application\Model\Image;

class ImageStatusType extends AbstractEnumType
{
    public function __construct()
    {
        $config = [
            Image::STATUS_NEW => 'New image',
            Image::STATUS_EDITED => 'Edited image',
            Image::STATUS_REVIEWED => 'Reviewed image',
        ];

        parent::__construct($config);
    }
}
