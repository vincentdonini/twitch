<?php

namespace App\Infrastructure\Doctrine\Filters;

use App\Domain\Common\Filter\Operator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Filters\FilterValue;
use App\Shared\Api\Doctrine\DoctrineFieldResolver;
use BackedEnum;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Uid\Uuid;

final class DoctrineFilterApplier
{
    private DoctrineFieldResolver $fieldResolver;

    public function __construct(?DoctrineFieldResolver $fieldResolver = null)
    {
        $this->fieldResolver = $fieldResolver ?? new DoctrineFieldResolver();
    }

    public function apply(
        QueryBuilder     $qb,
        string           $rootAlias,
        FilterCollection $filters
    ): QueryBuilder {
        foreach ($filters as $filter) {
            $this->applyFilter($qb, $rootAlias, $filter);
        }

        return $qb;
    }

    private function applyFilter(
        QueryBuilder $qb,
        string       $rootAlias,
        FilterValue  $filter
    ): void {
        $dqlField = $this->fieldResolver->resolve(
            $qb,
            $filter->field,
            $rootAlias
        );

        $operator = $filter->operator;
        $value    = $filter->value;

        $operatorValue = $operator instanceof BackedEnum
            ? $operator->value
            : $operator;

        $paramBase = str_replace('.', '_', $filter->field) . '_' . $operatorValue;

        $isUuid = $this->isUuidField($qb, $dqlField);

        match ($operator) {
            Operator::EQ       => $qb
                ->andWhere($isUuid ? "$dqlField = :$paramBase" : "LOWER($dqlField) = :$paramBase")
                ->setParameter($paramBase, $isUuid ? Uuid::fromString($value)->toBinary() : strtolower($value)),

            Operator::NEQ      => $qb
                ->andWhere($isUuid ? "$dqlField != :$paramBase" : "LOWER($dqlField) != :$paramBase")
                ->setParameter($paramBase, $isUuid ? Uuid::fromString($value)->toBinary() : strtolower($value)),

            Operator::LIKE     => !$isUuid ? $qb
                ->andWhere("LOWER($dqlField) LIKE :$paramBase")
                ->setParameter($paramBase, '%' . strtolower($value) . '%') : null,

            Operator::NOT_LIKE => !$isUuid ? $qb
                ->andWhere("LOWER($dqlField) NOT LIKE :$paramBase")
                ->setParameter($paramBase, '%' . strtolower($value) . '%') : null,

            Operator::LT       => !$isUuid ? $qb
                ->andWhere("$dqlField < :$paramBase")
                ->setParameter($paramBase, $value) : null,

            Operator::LTE      => !$isUuid ? $qb
                ->andWhere("$dqlField <= :$paramBase")
                ->setParameter($paramBase, $value) : null,

            Operator::GT       => !$isUuid ? $qb
                ->andWhere("$dqlField > :$paramBase")
                ->setParameter($paramBase, $value) : null,

            Operator::GTE      => !$isUuid ? $qb
                ->andWhere("$dqlField >= :$paramBase")
                ->setParameter($paramBase, $value) : null,

            Operator::IN       => $qb
                ->andWhere("$dqlField IN (:$paramBase)")
                ->setParameter($paramBase, $isUuid
                    ? array_map(fn($v) => Uuid::fromString(trim($v))->toBinary(), (array)$value)
                    : (array)$value),

            Operator::NOT_IN   => $qb
                ->andWhere("$dqlField NOT IN (:$paramBase)")
                ->setParameter($paramBase, $isUuid
                    ? array_map(fn($v) => Uuid::fromString(trim($v))->toBinary(), (array)$value)
                    : (array)$value),

            Operator::BETWEEN  => $this->applyBetween(
                $qb,
                $dqlField,
                $paramBase,
                $value
            ),

            default            => null
        };
    }

    private function isUuidField(QueryBuilder $qb, string $field): bool
    {
        $em   = $qb->getEntityManager();
        $root = $qb->getRootEntities()[0];
        $meta = $em->getClassMetadata($root);

        $parts     = explode('.', $field);
        $fieldName = end($parts);

        if (!$meta->hasField($fieldName)) {
            return false;
        }

        return in_array($meta->getTypeOfField($fieldName), ['uuid', 'uuid_binary', 'guid'], true);
    }

    private function applyBetween(
        QueryBuilder $qb,
        string       $dqlField,
        string       $paramBase,
        mixed        $value
    ): void {
        if (!is_array($value) || count($value) !== 2) {
            return;
        }

        $qb->andWhere("$dqlField BETWEEN :{$paramBase}_1 AND :{$paramBase}_2")
            ->setParameter("{$paramBase}_1", $value[0])
            ->setParameter("{$paramBase}_2", $value[1]);
    }
}
