<?php

declare(strict_types=1);

namespace ApplicationTest\Repository;

use Application\Model\Institution;
use Application\Repository\InstitutionRepository;

/**
 * @group Repository
 */
class InstitutionRepositoryTest extends AbstractRepositoryTest
{
    /**
     * @var InstitutionRepository
     */
    private $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = _em()->getRepository(Institution::class);
    }

    public function testGetOrCreateByName(): void
    {
        $institution = $this->repository->getOrCreateByName('Test institution 5000');
        self::assertSame('Test institution 5000', $institution->getName());
        self::assertSame(5000, $institution->getId());

        $institution = $this->repository->getOrCreateByName('Test institution 5000    ');
        self::assertSame('Test institution 5000', $institution->getName(), 'whitespace should not matter');
        self::assertSame(5000, $institution->getId());

        $institution = $this->repository->getOrCreateByName('Test foo');
        self::assertSame('Test foo', $institution->getName());
        self::assertNull($institution->getId());

        $institution = $this->repository->getOrCreateByName('Test foo    ');
        self::assertSame('Test foo', $institution->getName(), 'whitespace should not matter');
        self::assertNull($institution->getId());

        $institution = $this->repository->getOrCreateByName('    ');
        self::assertNull($institution, 'should not create with empty name');

        $institution = $this->repository->getOrCreateByName(null);
        self::assertNull($institution, 'should not create with null name');
    }
}
