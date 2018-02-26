<?php

declare(strict_types=1);

namespace ApplicationTest\Repository;

use Application\Model\Card;
use Application\Model\Change;
use Application\Repository\ChangeRepository;

/**
 * @group Repository
 */
class ChangeRepositoryTest extends AbstractRepositoryTest
{
    /**
     * @var ChangeRepository
     */
    private $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = _em()->getRepository(Change::class);
    }

    public function testGetOpenChange(): void
    {
        $request = '';
        $creationSuggestion = _em()->getReference(Card::class, 6001);
        $updateSuggestion = _em()->getReference(Card::class, 6002);
        $deletionSuggestion = _em()->getReference(Card::class, 6000);

        // Can retrieve existing one
        self::assertNotNull($this->repository->getOrCreate(Change::TYPE_CREATE, $creationSuggestion, $request)->getId());
        self::assertNotNull($this->repository->getOrCreate(Change::TYPE_UPDATE, $updateSuggestion, $request)->getId());
        self::assertNotNull($this->repository->getOrCreate(Change::TYPE_DELETE, $deletionSuggestion, $request)->getId());

        // Can create new one
        self::assertNull($this->repository->getOrCreate(Change::TYPE_UPDATE, $creationSuggestion, $request)->getId());
        self::assertNull($this->repository->getOrCreate(Change::TYPE_DELETE, $creationSuggestion, $request)->getId());
        self::assertNull($this->repository->getOrCreate(Change::TYPE_CREATE, $updateSuggestion, $request)->getId());
        self::assertNull($this->repository->getOrCreate(Change::TYPE_DELETE, $updateSuggestion, $request)->getId());
        self::assertNull($this->repository->getOrCreate(Change::TYPE_CREATE, $deletionSuggestion, $request)->getId());
        self::assertNull($this->repository->getOrCreate(Change::TYPE_UPDATE, $deletionSuggestion, $request)->getId());
    }
}
