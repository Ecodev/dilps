<?php

declare(strict_types=1);

namespace ApplicationTest\Model;

use Application\Model\Collection;
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
        $this->assertSame('', $withoutName->getName());

        $withName = new Image('test name');
        $this->assertSame('test name', $withName->getName());
    }

    public function testGetPath(): void
    {
        $image = new Image();
        $image->setFilename('photo.jpg');

        $this->assertSame('photo.jpg', $image->getFilename());
        $appPath = realpath('.');
        $expected = $appPath . '/data/images/photo.jpg';
        $this->assertSame($expected, $image->getPath());

        $expected = $appPath . '/data/images/small/photo.jpg';
        $this->assertSame($expected, $image->getSmallPath());
    }

    public function testCollectionRelation(): void
    {
        $collection = new Collection();
        $image = new Image();

        $this->assertCount(0, $collection->getImages());

        $image->setCollection($collection);

        $this->assertSame($collection, $image->getCollection());
        $this->assertSame($image, $collection->getImages()[0]);
    }
}
