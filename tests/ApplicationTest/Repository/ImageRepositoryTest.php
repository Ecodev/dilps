<?php

declare(strict_types=1);

namespace ApplicationTest\Repository;

use Application\Model\Collection;
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
        $collection = _em()->getReference(Collection::class, 2000);
        $image = new Image('test image');
        $image->setCollection($collection);

        $image->setFilename('test image.jpg');
        $this->getEntityManager()->persist($image);
        $this->getEntityManager()->flush();

        touch($image->getPath());
        touch($image->getSmallPath());
        $this->assertFileExists($image->getPath(), 'test file must exist, because we just touch()ed it');
        $this->assertFileExists($image->getSmallPath(), 'thumb file must exist, because we just touch()ed it');

        $this->getEntityManager()->remove($image);
        $this->getEntityManager()->flush();
        $this->assertFileNotExists($image->getPath(), 'test file must have been deleted when record was deleted');
        $this->assertFileNotExists($image->getSmallPath(), 'thumb file must have been deleted when record was deleted');
    }
}
