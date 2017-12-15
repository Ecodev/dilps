<?php

declare(strict_types=1);

namespace ApplicationTest\Repository;

use Application\Model\Image;
use ApplicationTest\Traits\TestWithTransaction;
use PHPUnit\Framework\TestCase;

/**
 * @group Repository
 */
class ImageRepositoryTest extends TestCase
{
    use TestWithTransaction;

    public function testImageOnDiskIsDeletedWhenRecordInDbIsDeleted(): void
    {
        $image = new Image('test image');

        $image->setFilename('test image.jpg');
        $this->getEntityManager()->persist($image);
        $this->getEntityManager()->flush();

        touch($image->getPath());
        self::assertFileExists($image->getPath(), 'test file must exist, because we just touch()ed it');

        $this->getEntityManager()->remove($image);
        $this->getEntityManager()->flush();
        self::assertFileNotExists($image->getPath(), 'test file must have been deleted when record was deleted');
    }
}
