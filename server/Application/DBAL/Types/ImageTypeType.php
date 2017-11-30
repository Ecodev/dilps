<?php

declare(strict_types=1);

namespace Application\DBAL\Types;

use Application\Model\Image;

class ImageTypeType extends AbstractEnumType
{
    protected function getPossibleValues(): array
    {
        return [
            Image::TYPE_DEFAULT,
            Image::TYPE_IMAGE,
            Image::TYPE_ARCHITECTURE,
        ];
    }
}
