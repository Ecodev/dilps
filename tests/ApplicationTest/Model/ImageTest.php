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

    public function testSetDating(): void
    {
        $image = new Image();

        self::assertCount(0, $image->getDatings());

        $image->setDating('2000');
        self::assertCount(1, $image->getDatings());
        $original = $image->getDatings()->first();

        $image->setDating('2000');
        self::assertCount(1, $image->getDatings());
        self::assertSame($original, $image->getDatings()->first(), 'must be same dating');

        $image->setDating('1980-1990');
        self::assertCount(1, $image->getDatings());
        self::assertNotSame($original, $image->getDatings()->first(), 'must be new one');

        $image->setDating('');
        self::assertCount(0, $image->getDatings());
    }
}
