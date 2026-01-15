<?php

namespace App\Infrastructure\Doctrine\Sorts;

use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Sorts\SortCollection;
use App\Infrastructure\Sorts\SortValue;
use App\Shared\Api\Doctrine\DoctrineFieldResolver;
use Doctrine\ORM\QueryBuilder;

final class DoctrineSortApplier
{
    use LocaleTrait;

    private DoctrineFieldResolver $fieldResolver;

    public function __construct(?DoctrineFieldResolver $fieldResolver = null)
    {
        $this->fieldResolver = $fieldResolver ?? new DoctrineFieldResolver();
    }

    public function apply(
        QueryBuilder   $qb,
        string         $rootAlias,
        SortCollection $sorts
    ): QueryBuilder {
        foreach ($sorts as $sort) {
            if (!$sort instanceof SortValue) {
                continue;
            }

            $dqlField = $this->fieldResolver->resolve(
                $qb,
                $sort->field,
                $rootAlias
            );

            $qb->addOrderBy(
                $dqlField,
                $sort->direction->value
            );
        }

        return $qb;
    }
}
