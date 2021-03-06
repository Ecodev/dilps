<?php

declare(strict_types=1);

namespace Application\Repository;

use Application\Model\Institution;
use Doctrine\ORM\QueryBuilder;

class InstitutionRepository extends AbstractRepository
{
    public function getFindAllQuery(array $filters = [], array $sorting = []): QueryBuilder
    {
        $qb = $this->createQueryBuilder('institution');

        $this->applySearch($qb, $filters, 'institution');
        $this->applySorting($qb, $sorting, 'institution');

        return $qb;
    }

    /**
     * Get or create an institution by its name
     *
     * @param null|string $name
     *
     * @return Institution
     */
    public function getOrCreateByName(?string $name): ?Institution
    {
        $name = trim($name ?? '');

        if (!$name) {
            return null;
        }

        $institution = $this->findOneByName($name);
        if (!$institution) {
            $institution = new Institution();
            $this->getEntityManager()->persist($institution);
            $institution->setName($name);
        }

        return $institution;
    }
}
