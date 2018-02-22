<?php

declare(strict_types=1);

namespace ApplicationTest\Model;

use Application\Model\Collection;
use Application\Model\Country;
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

    public function testCopyInto(): void
    {
        $collection = new Collection();
        $image1 = new Image();
        $image1->setName('test name');
        $image1->setDating('2010');
        $image1->setArtists(['John', 'Sarah']);
        $image1->setInstitution('Museum');
        $image1->setCountry(new Country());
        $image1->timestampCreation();
        $collection->addImage($image1);
        $image1->setFilename('foo.png');
        touch($image1->getPath());

        $image2 = new Image();
        $image1->setOriginal($image2);
        $image1->copyInto($image2);

        self::assertSame('test name', $image2->getName());
        self::assertSame('2010', $image2->getDating());
        self::assertSame('2010-01-01T00:00:00+00:00', $image2->getDatings()->first()->getFrom()->format('c'), 'datings should be re-computed');
        self::assertCount(2, $image2->getArtists(), 'artists should be copied');
        self::assertSame('Museum', $image2->getInstitution()->getName(), 'institution should be copied');
        self::assertNotNull($image2->getCountry(), 'country should be copied');
        self::assertNull($image2->getOriginal(), 'original should not be copied over');
        self::assertCount(1, $collection->getImages(), 'image2 should not be moved to intro a collection');

        self::assertNotEquals('', $image2->getFilename(), 'should have file on disk');
        self::assertNotEquals($image1->getFilename(), $image2->getFilename(), 'should not share the same file on disk');
        self::assertFileExists($image2->getPath(), 'file on disk should have been copied');
    }
}
