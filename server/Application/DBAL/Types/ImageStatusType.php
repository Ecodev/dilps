<?php

declare(strict_types=1);

namespace Application\DBAL\Types;

use Application\Model\Image;

class ImageStatusType extends AbstractEnumType
{
    protected function getPossibleValues(): array
    {
        return [
            Image::STATUS_NEW,
            Image::STATUS_EDITED,
            Image::STATUS_REVIEWED,
        ];
    }
}
