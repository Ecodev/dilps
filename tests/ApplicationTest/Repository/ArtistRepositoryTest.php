<?php

declare(strict_types=1);

namespace ApplicationTest\Repository;

use Application\Model\Artist;
use Application\Repository\ArtistRepository;

/**
 * @group Repository
 */
class ArtistRepositoryTest extends AbstractRepositoryTest
{
    /**
     * @var ArtistRepository
     */
    private $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = _em()->getRepository(Artist::class);
    }

    public function testGetOrCreateByNames(): void
    {
        $names = [
            'Test artist 3000',
            'Test foo',
            'Test foo', // duplicate
            'Test foo ', // duplicate with whitespace
        ];
        $artists = $this->repository->getOrCreateByNames($names);

        self::assertCount(2, $artists);
        self::assertSame('Test artist 3000', $artists[0]->getName());
        self::assertSame(3000, $artists[0]->getId());

        self::assertSame('Test foo', $artists[1]->getName());
        self::assertNull($artists[1]->getId());
    }
}
