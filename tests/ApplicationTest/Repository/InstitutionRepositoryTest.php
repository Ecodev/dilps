<?php

declare(strict_types=1);

namespace ApplicationTest\Repository;

use Application\Model\Institution;
use ApplicationTest\Traits\TestWithTransaction;
use PHPUnit\Framework\TestCase;

/**
 * @group Repository
 */
class InstitutionRepositoryTest extends TestCase
{
    use TestWithTransaction;

    public function testGetOrCreateByName(): void
    {
        $repository = $this->getEntityManager()->getRepository(Institution::class);

        $institution = $repository->getOrCreateByName('Test institution 5000');
        self::assertSame('Test institution 5000', $institution->getName());
        self::assertSame(5000, $institution->getId());

        $institution = $repository->getOrCreateByName('Test institution 5000    ');
        self::assertSame('Test institution 5000', $institution->getName(), 'whitespace should not matter');
        self::assertSame(5000, $institution->getId());

        $institution = $repository->getOrCreateByName('Test foo');
        self::assertSame('Test foo', $institution->getName());
        self::assertNull($institution->getId());

        $institution = $repository->getOrCreateByName('Test foo    ');
        self::assertSame('Test foo', $institution->getName(), 'whitespace should not matter');
        self::assertNull($institution->getId());

        $institution = $repository->getOrCreateByName('    ');
        self::assertNull($institution, 'should not create with empty name');

        $institution = $repository->getOrCreateByName(null);
        self::assertNull($institution, 'should not create with null name');
    }
}
