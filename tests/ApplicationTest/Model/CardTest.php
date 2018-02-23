<?php

declare(strict_types=1);

namespace ApplicationTest\Model;

use Application\Model\Card;
use Application\Model\Collection;
use Application\Model\Country;
use PHPUnit\Framework\TestCase;

/**
 * @group Model
 */
class CardTest extends TestCase
{
    public function testName(): void
    {
        $withoutName = new Card();
        self::assertSame('', $withoutName->getName());

        $withName = new Card('test name');
        self::assertSame('test name', $withName->getName());
    }

    public function testGetPath(): void
    {
        $card = new Card();
        $card->setFilename('photo.jpg');

        self::assertSame('photo.jpg', $card->getFilename());
        $appPath = realpath('.');
        $expected = $appPath . '/data/images/photo.jpg';
        self::assertSame($expected, $card->getPath());
    }

    public function testSetDating(): void
    {
        $card = new Card();

        self::assertCount(0, $card->getDatings());

        $card->setDating('2000');
        self::assertCount(1, $card->getDatings());
        $original = $card->getDatings()->first();

        $card->setDating('2000');
        self::assertCount(1, $card->getDatings());
        self::assertSame($original, $card->getDatings()->first(), 'must be same dating');

        $card->setDating('1980-1990');
        self::assertCount(1, $card->getDatings());
        self::assertNotSame($original, $card->getDatings()->first(), 'must be new one');

        $card->setDating('');
        self::assertCount(0, $card->getDatings());
    }

    public function testCopyInto(): void
    {
        $collection = new Collection();
        $card1 = new Card();
        $card1->setName('test name');
        $card1->setDating('2010');
        $card1->setArtists(['John', 'Sarah']);
        $card1->setInstitution('Museum');
        $card1->setCountry(new Country());
        $card1->timestampCreation();
        $collection->addCard($card1);
        $card1->setFilename('foo.png');
        touch($card1->getPath());

        $card2 = new Card();
        $card1->setOriginal($card2);
        $card1->copyInto($card2);

        self::assertSame('test name', $card2->getName());
        self::assertSame('2010', $card2->getDating());
        self::assertSame('2010-01-01T00:00:00+00:00', $card2->getDatings()->first()->getFrom()->format('c'), 'datings should be re-computed');
        self::assertCount(2, $card2->getArtists(), 'artists should be copied');
        self::assertSame('Museum', $card2->getInstitution()->getName(), 'institution should be copied');
        self::assertNotNull($card2->getCountry(), 'country should be copied');
        self::assertNull($card2->getOriginal(), 'original should not be copied over');
        self::assertCount(1, $collection->getCards(), 'card2 should not be moved to intro a collection');

        self::assertNotEquals('', $card2->getFilename(), 'should have file on disk');
        self::assertNotEquals($card1->getFilename(), $card2->getFilename(), 'should not share the same file on disk');
        self::assertFileExists($card2->getPath(), 'file on disk should have been copied');
    }

    public function testRelatedCards(): void
    {
        $card1 = new Card();
        $card2 = new Card();

        self::assertCount(0, $card1->getCards());
        self::assertCount(0, $card2->getCards());

        $card1->addCard($card2);

        self::assertCount(1, $card1->getCards());
        self::assertCount(1, $card2->getCards());

        self::assertSame($card2, $card1->getCards()->first());
        self::assertSame($card1, $card2->getCards()->first());

        $card2->removeCard($card1);

        self::assertCount(0, $card1->getCards());
        self::assertCount(0, $card2->getCards());
    }
}
