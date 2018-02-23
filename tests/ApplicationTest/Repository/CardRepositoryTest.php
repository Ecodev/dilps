<?php

declare(strict_types=1);

namespace ApplicationTest\Repository;

use Application\Model\Card;
use ApplicationTest\Traits\TestWithTransaction;
use PHPUnit\Framework\TestCase;

/**
 * @group Repository
 */
class CardRepositoryTest extends TestCase
{
    use TestWithTransaction;

    public function testCardOnDiskIsDeletedWhenRecordInDbIsDeleted(): void
    {
        $card = new Card('test card');

        $card->setFilename('test card.jpg');
        $this->getEntityManager()->persist($card);
        $this->getEntityManager()->flush();

        touch($card->getPath());
        self::assertFileExists($card->getPath(), 'test file must exist, because we just touch()ed it');

        $this->getEntityManager()->remove($card);
        $this->getEntityManager()->flush();
        self::assertFileNotExists($card->getPath(), 'test file must have been deleted when record was deleted');
    }
}
