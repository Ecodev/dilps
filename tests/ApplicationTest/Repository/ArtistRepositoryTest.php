<?php

declare(strict_types=1);

namespace ApplicationTest\Repository;

use Application\Model\Artist;
use ApplicationTest\Traits\TestWithTransaction;
use PHPUnit\Framework\TestCase;

/**
 * @group Repository
 */
class ArtistRepositoryTest extends TestCase
{
    use TestWithTransaction;

    public function testGetOrCreateByNames(): void
    {
        $names = [
            'Test artist 3000',
            'Test foo',
            'Test foo', // duplicate
            'Test foo ', // duplicate with whitespace
        ];
        $artists = $this->getEntityManager()->getRepository(Artist::class)->getOrCreateByNames($names);

        self::assertCount(2, $artists);
        self::assertSame('Test artist 3000', $artists[0]->getName());
        self::assertSame(3000, $artists[0]->getId());

        self::assertSame('Test foo', $artists[1]->getName());
        self::assertNull($artists[1]->getId());
    }
}
