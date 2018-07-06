<?php

declare(strict_types=1);

namespace Application\Api\Input\Operator;

use Application\Model\Dating;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use GraphQL\Doctrine\Definition\Operator\AbstractOperator;
use GraphQL\Doctrine\Factory\UniqueNameFactory;
use GraphQL\Type\Definition\LeafType;

class YearRangeOperatorType extends AbstractOperator
{
    protected function getConfiguration(LeafType $leafType): array
    {
        return [
            'fields' => [
                [
                    'name' => 'from',
                    'type' => self::nonNull(self::int()),
                ],
                [
                    'name' => 'to',
                    'type' => self::nonNull(self::int()),
                ],
            ],
        ];
    }

    public function getDqlCondition(UniqueNameFactory $uniqueNameFactory, ClassMetadata $metadata, QueryBuilder $queryBuilder, string $alias, string $field, ?array $args): ?string
    {
        if (!$args) {
            return null;
        }

        $datings = $uniqueNameFactory->createAliasName(Dating::class);
        $queryBuilder->leftJoin($alias . '.datings', $datings);

        $from = $uniqueNameFactory->createParameterName();
        $to = $uniqueNameFactory->createParameterName();

        $queryBuilder->setParameter($from, $this->yearToJulian($args['from']));
        $queryBuilder->setParameter($to, $this->yearToJulian($args['to'], true));

        return "((:$from >= $datings.from AND :$from <= $datings.to) OR (:$to <= $datings.to AND :$to >= $datings.from) OR (:$from <= $datings.from AND :$to >= $datings.to))";
    }

    private function yearToJulian(int $year, bool $end = false): int
    {
        if ($end) {
            $day = 31;
            $month = 12;
        } else {
            $day = 1;
            $month = 1;
        }

        return gregoriantojd($month, $day, $year);
    }
}
