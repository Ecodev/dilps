<?php

declare(strict_types=1);

namespace ApplicationTest\Model;

use Application\Model\Image;
use PHPUnit\Framework\TestCase;

/**
 * @group Model
 */
class ImageTest extends TestCase
{
    public function testName(): void
    {
        $withoutName = new Image();
        self::assertSame('', $withoutName->getName());

        $withName = new Image('test name');
        self::assertSame('test name', $withName->getName());
    }

    public function testGetPath(): void
    {
        $image = new Image();
        $image->setFilename('photo.jpg');

        self::assertSame('photo.jpg', $image->getFilename());
        $appPath = realpath('.');
        $expected = $appPath . '/data/images/photo.jpg';
        self::assertSame($expected, $image->getPath());
    }
}
