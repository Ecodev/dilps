<?php

declare(strict_types=1);

namespace ApplicationTest\Repository;

use Application\Model\Card;
use Application\Model\Change;
use ApplicationTest\Traits\TestWithTransaction;
use PHPUnit\Framework\TestCase;

/**
 * @group Repository
 */
class ChangeRepositoryTest extends TestCase
{
    use TestWithTransaction;

    public function testGetOpenChange(): void
    {
        $request = '';
        $creationSuggestion = _em()->getReference(Card::class, 6001);
        $updateSuggestion = _em()->getReference(Card::class, 6002);
        $deletionSuggestion = _em()->getReference(Card::class, 6000);
        $repository = $this->getEntityManager()->getRepository(Change::class);

        // Can retrieve existing one
        self::assertNotNull($repository->getOrCreate(Change::TYPE_CREATE, $creationSuggestion, $request)->getId());
        self::assertNotNull($repository->getOrCreate(Change::TYPE_UPDATE, $updateSuggestion, $request)->getId());
        self::assertNotNull($repository->getOrCreate(Change::TYPE_DELETE, $deletionSuggestion, $request)->getId());

        // Can create new one
        self::assertNull($repository->getOrCreate(Change::TYPE_UPDATE, $creationSuggestion, $request)->getId());
        self::assertNull($repository->getOrCreate(Change::TYPE_DELETE, $creationSuggestion, $request)->getId());
        self::assertNull($repository->getOrCreate(Change::TYPE_CREATE, $updateSuggestion, $request)->getId());
        self::assertNull($repository->getOrCreate(Change::TYPE_DELETE, $updateSuggestion, $request)->getId());
        self::assertNull($repository->getOrCreate(Change::TYPE_CREATE, $deletionSuggestion, $request)->getId());
        self::assertNull($repository->getOrCreate(Change::TYPE_UPDATE, $deletionSuggestion, $request)->getId());
    }
}
