<?php

declare(strict_types=1);

namespace ApplicationTest\Model;

use Application\Model\Card;
use Application\Model\Collection;
use Application\Model\Country;
use Application\Model\User;
use Application\Utility;
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
        $suggestion = new Card();
        $suggestion->setName('test name');
        $suggestion->setDating('2010');
        $suggestion->setArtists(['John', 'Sarah']);
        $suggestion->setInstitution('Museum');
        $suggestion->setCountry(new Country());
        $suggestion->timestampCreation();
        $suggestion->setWidth(123);
        $suggestion->setHeight(123);
        $suggestion->setFileSize(123);
        $collection->addCard($suggestion);
        $suggestion->setFilename('foo.png');
        touch($suggestion->getPath());

        $original = new Card();
        $original->setWidth(456);
        $original->setHeight(456);
        $original->setFileSize(456);
        $suggestion->setOriginal($original);
        $suggestion->copyInto($original);

        self::assertSame('test name', $original->getName());
        self::assertSame('2010', $original->getDating());
        self::assertSame('2010-01-01T00:00:00+00:00', $original->getDatings()->first()->getFrom()->format('c'), 'datings should be re-computed');
        self::assertCount(2, $original->getArtists(), 'artists should be copied');
        self::assertSame('Museum', $original->getInstitution()->getName(), 'institution should be copied');
        self::assertNotNull($original->getCountry(), 'country should be copied');
        self::assertNull($original->getOriginal(), 'original should not be copied over');
        self::assertCount(1, $collection->getCards(), 'original should not be moved to intro a collection');

        self::assertNotEquals('', $original->getFilename(), 'should have file on disk');
        self::assertNotEquals($suggestion->getFilename(), $original->getFilename(), 'should not share the same file on disk');
        self::assertFileExists($original->getPath(), 'file on disk should have been copied');

        self::assertSame(123, $original->getWidth());
        self::assertSame(123, $original->getHeight());
        self::assertSame(123, $original->getFileSize());
    }

    public function testCopyIntoWithoutFile(): void
    {
        $original = new Card();
        $original->setWidth(456);
        $original->setHeight(456);
        $original->setFileSize(456);

        $suggestion = new Card();
        $suggestion->copyInto($original);

        self::assertSame(456, $original->getWidth());
        self::assertSame(456, $original->getHeight());
        self::assertSame(456, $original->getFileSize());
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

    public function testRelatedCardWithSelf(): void
    {
        $this->expectExceptionMessage('A card cannot be related to itself');
        $card = new Card();
        $card->addCard($card);
    }

    public function testValidation(): void
    {
        $user = new User();
        User::setCurrent($user);
        $card = new Card();

        self::assertNull($card->getDataValidationDate());
        self::assertNull($card->getDataValidator());
        self::assertNull($card->getImageValidationDate());
        self::assertNull($card->getImageValidator());

        $card->validateData();
        self::assertSame(Utility::getNow(), $card->getDataValidationDate());
        self::assertSame($user, $card->getDataValidator());
        self::assertNull($card->getImageValidationDate());
        self::assertNull($card->getImageValidator());

        $card->validateImage();
        self::assertSame(Utility::getNow(), $card->getDataValidationDate());
        self::assertSame($user, $card->getDataValidator());
        self::assertSame(Utility::getNow(), $card->getImageValidationDate());
        self::assertSame($user, $card->getImageValidator());
    }
}
