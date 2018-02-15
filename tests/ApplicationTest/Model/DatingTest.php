<?php

declare(strict_types=1);

namespace ApplicationTest\Model;

use Application\Model\Dating;
use Application\Model\Image;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

/**
 * @group Model
 */
class DatingTest extends TestCase
{
    public function testFrom(): void
    {
        $dating = new Dating();
        $d1 = new DateTimeImmutable('2010-02-03', new DateTimeZone('UTC'));
        $dating->setFrom($d1);
        $d2 = $dating->getFrom();

        $this->assertNotSame($d1, $d2);
        $this->assertEquals($d1, $d2);
    }

    public function testTo(): void
    {
        $dating = new Dating();
        $d1 = new DateTimeImmutable('2010-02-03', new DateTimeZone('UTC'));
        $dating->setTo($d1);
        $d2 = $dating->getTo();

        $this->assertNotSame($d1, $d2);
        $this->assertEquals($d1, $d2);
    }

    public function testDatingRelation(): void
    {
        $image = new Image();
        self::assertCount(0, $image->getDatings(), 'should have no datings');

        $dating = new Dating();
        $dating->setImage($image);

        self::assertSame($image, $dating->getImage(), 'should belong to the image');
        self::assertCount(1, $image->getDatings(), 'should have the added dating');
        self::assertSame($dating, $image->getDatings()->first(), 'should be able to retrieve added dating');

        $dating->setImage($image);
        self::assertSame($image, $dating->getImage(), 'should still belong to exactly 1 image');
        self::assertCount(1, $image->getDatings(), 'should still have the same unique dating');

        $dating2 = new Dating();
        $dating2->setImage($image);
        self::assertCount(2, $image->getDatings(), 'should be able to add second dating');

        $dating->setImage(new Image());
        self::assertCount(1, $image->getDatings(), 'should be able to remove first dating');
        self::assertSame($dating2, $image->getDatings()->first(), 'should be have only second dating left');
    }
}
