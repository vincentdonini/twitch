<?php

namespace App\Infrastructure\Doctrine\Filters;

use App\Domain\Common\Filter\Operator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Filters\FilterValue;
use App\Shared\Api\Doctrine\DoctrineFieldResolver;
use BackedEnum;
use Doctrine\ORM\QueryBuilder;

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

        match ($operator) {
            Operator::EQ       => $qb
                ->andWhere("LOWER($dqlField) = :$paramBase")
                ->setParameter($paramBase, strtolower($value)),

            Operator::NEQ      => $qb
                ->andWhere("LOWER($dqlField) != :$paramBase")
                ->setParameter($paramBase, strtolower($value)),

            Operator::LIKE     => $qb
                ->andWhere("LOWER($dqlField) LIKE :$paramBase")
                ->setParameter($paramBase, '%' . strtolower($value) . '%'),

            Operator::NOT_LIKE => $qb
                ->andWhere("LOWER($dqlField) NOT LIKE :$paramBase")
                ->setParameter($paramBase, '%' . strtolower($value) . '%'),

            Operator::LT       => $qb
                ->andWhere("$dqlField < :$paramBase")
                ->setParameter($paramBase, $value),

            Operator::LTE      => $qb
                ->andWhere("$dqlField <= :$paramBase")
                ->setParameter($paramBase, $value),

            Operator::GT       => $qb
                ->andWhere("$dqlField > :$paramBase")
                ->setParameter($paramBase, $value),

            Operator::GTE      => $qb
                ->andWhere("$dqlField >= :$paramBase")
                ->setParameter($paramBase, $value),

            Operator::IN       => $qb
                ->andWhere("$dqlField IN (:$paramBase)")
                ->setParameter($paramBase, (array)$value),

            Operator::NOT_IN   => $qb
                ->andWhere("$dqlField NOT IN (:$paramBase)")
                ->setParameter($paramBase, (array)$value),

            Operator::BETWEEN  => $this->applyBetween(
                $qb,
                $dqlField,
                $paramBase,
                $value
            ),

            default            => null
        };
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
