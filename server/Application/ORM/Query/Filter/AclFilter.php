<?php

declare(strict_types=1);

namespace Application\ORM\Query\Filter;

use Application\Model\User;
use Application\Repository\LimitedAccessSubQueryInterface;
use Doctrine\ORM\Mapping\ClassMetaData;
use Doctrine\ORM\Query\Filter\SQLFilter;

/**
 * Automatically filter objects according to what user is allowed to access.
 *
 * Here a few cases to consider:
 *
 * - Most objects will be filtered with subqueries because they implement LimitedAccessSubQueryInterface
 * - Some "leaf" objects will be declared in this class and use their owner's subquery to filter
 * - A minority, only `Group` so far, will use a list of ID instead of subquery
 */
class AclFilter extends SQLFilter
{
    /**
     * @var null|User
     */
    private $user;

    /**
     * Cache of subqueries to get accessible IDs.
     * Keys are class name of object
     *
     * @var array possible values of the array are: null|string
     */
    private $subQueriesCache = [];

    /**
     * Whether the filter is active
     *
     * @var bool
     */
    private $enabled = true;

    /**
     * Enable or disable the filter
     *
     * This method MUST be used instead of `$entityManager->getFilters()->enable()` because
     * that method destroy the filter object and thus losing the current user. So to keep
     * our internal state intact we must implement a custom enable/disable mechanism.
     *
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @param ClassMetaData $targetEntity
     * @param string $targetTableAlias
     *
     * @return string
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        if (!$this->enabled) {
            return '';
        }

        $repository = _em()->getRepository($targetEntity->name);
        $result = '';
        if ($repository instanceof LimitedAccessSubQueryInterface) {
            $subQuery = $this->getSubQuery($targetEntity->name);
            $result = $this->buildSql($targetTableAlias, 'id', $subQuery);
        }

        return $result;
    }

    /**
     * Set the current user for which we should apply access limitations
     *
     * @param null|User $user
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
        $this->resetCache();
        _em()->getConfiguration()->getQueryCacheImpl()->deleteAll();
    }

    private function resetCache(): void
    {
        $this->subQueriesCache = [];
    }

    private function buildSql(string $targetTableAlias, string $field, ?string $subQuery): string
    {
        if ($subQuery === null) {
            return '';
        }

        return $targetTableAlias . '.' . $field . ' IN (' . $subQuery . ')';
    }

    private function getSubQuery(string $class): ?string
    {
        // Administrator can always access everything
        if (!array_key_exists($class, $this->subQueriesCache) && $this->user && $this->user->getRole() === User::ROLE_ADMINISTRATOR) {
            $this->subQueriesCache[$class] = null;
        }

        // Other users need subqueries
        if (!array_key_exists($class, $this->subQueriesCache)) {
            $subQuery = _em()->getRepository($class)->getAccessibleSubQuery($this->user);
            $this->subQueriesCache[$class] = $subQuery;
        }

        return $this->subQueriesCache[$class];
    }
}
