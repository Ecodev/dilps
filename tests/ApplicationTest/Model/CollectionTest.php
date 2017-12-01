<?php

declare(strict_types=1);

namespace ApplicationTest\Model;

use Application\Model\Collection;
use Application\Model\Image;
use InvalidArgumentException;

class CollectionTest extends \PHPUnit\Framework\TestCase
{
    public function testChildCollectionsRelation(): void
    {
        $parent = new Collection();
        $child = new Collection();
        self::assertCount(0, $parent->getChildren());

        $child->setParent($parent);
        self::assertCount(1, $parent->getChildren());
        self::assertSame($child, $parent->getChildren()[0]);

        $otherParent = new Collection();
        self::assertCount(0, $otherParent->getChildren());

        $child->setParent($otherParent);
        self::assertCount(0, $parent->getChildren());
        self::assertCount(1, $otherParent->getChildren());
        self::assertSame($child, $otherParent->getChildren()[0]);

        $child->setParent(null);
        self::assertCount(0, $parent->getChildren());
        self::assertCount(0, $otherParent->getChildren());
    }

    public function testCannotCreateCyclicHierarchy(): void
    {
        $parent = new Collection();
        $child = new Collection();
        $grandChild = new Collection();

        $child->setParent($parent);
        $grandChild->setParent($child);

        self::expectException(InvalidArgumentException::class);
        $parent->setParent($grandChild);
    }

    public function testImageRelation(): void
    {
        $collection = new Collection();
        self::assertCount(0, $collection->getImages(), 'should have no images');

        $image = new Image();
        self::assertCount(0, $image->getCollections(), 'should belong to no collection');

        $collection->addImage($image);
        self::assertCount(1, $image->getCollections(), 'should belong to the collection');
        self::assertSame($collection, $image->getCollections()->first(), 'should belong to the same collection');
        self::assertCount(1, $collection->getImages(), 'should have the added image');
        self::assertSame($image, $collection->getImages()->first(), 'should be able to retrieve added image');

        $collection->addImage($image);
        self::assertCount(1, $image->getCollections(), 'should still belong to exactly 1 collection');
        self::assertCount(1, $collection->getImages(), 'should still have the same unique image');

        $image2 = new Image();
        $collection->addImage($image2);
        self::assertCount(2, $collection->getImages(), 'should be able to add second image');

        $collection->removeImage($image);
        self::assertCount(0, $image->getCollections(), 'should not belong to any collection anymore');
        self::assertCount(1, $collection->getImages(), 'should be able to remove first image');
        self::assertSame($image2, $collection->getImages()->first(), 'should be have only second image left');
    }
}
