<?php

namespace App\Infrastructure\Doctrine\Repository\Wod;

use App\Domain\Common\Filter\Operator;
use App\Domain\Wod\Entity\Wod;
use App\Domain\Wod\Entity\WodVariantExercise;
use App\Domain\Wod\Ports\WodDALInterface;
use App\Infrastructure\Doctrine\Filters\DoctrineFilterApplier;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Doctrine\Repository\AbstractEntityRepository;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Doctrine\Sorts\DoctrineSortApplier;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Uid\Uuid;

class WodRepository extends AbstractEntityRepository implements WodDALInterface
{
    use LocaleTrait;

    public function __construct(
        protected ManagerRegistry     $registry,
        private readonly RequestStack $requestStack,
        private readonly string       $locale,
    ) {
        parent::__construct($registry);
    }

    public function getClass(): string
    {
        return Wod::class;
    }

    public function getManager(): string
    {
        return Wod::class;
    }

    public function getById(Uuid $id): ?Wod
    {
        return $this->find($id);
    }

    public function getByName(string $name): ?Wod
    {
        return $this->createQueryBuilder('w')
            ->where('w.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function listWods(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null,
    ): LightPaginator {
        $qb = $this->createQueryBuilder('w');

        // Filters
        // -------------------------------------------------------------------------------------------------------------
        // Exercise exclusion (NEQ/NOT_IN) requires a NOT EXISTS subquery — a LEFT JOIN + NOT IN doesn't work on
        // one-to-many paths because sibling rows (other exercises of the same Wod) satisfy the condition.
        $exerciseExcludeIds         = [];
        $exerciseCategoryExcludeIds = [];
        $equipmentExcludeIds        = [];
        $muscleExcludeIds           = [];
        $muscleGroupExcludeIds      = [];
        $muscleAreaExcludeIds       = [];
        $muscleSegmentExcludeIds    = [];
        $standardFilters            = [];

        foreach ($filters as $filter) {
            if (
                $filter->field === 'wodVariants.wodVariantExercises.exercise.id' &&
                in_array($filter->operator, [Operator::NEQ, Operator::NOT_IN], true)
            ) {
                foreach ((array)$filter->value as $v) {
                    $exerciseExcludeIds[] = Uuid::fromString(trim($v))->toBinary();
                }
            } elseif (
                $filter->field === 'wodVariants.wodVariantExercises.exercise.exerciseCategory.id' &&
                in_array($filter->operator, [Operator::NEQ, Operator::NOT_IN], true)
            ) {
                foreach ((array)$filter->value as $v) {
                    $exerciseCategoryExcludeIds[] = Uuid::fromString(trim($v))->toBinary();
                }
            } elseif (
                $filter->field === 'wodVariants.wodVariantExercises.exercise.equipment.id' &&
                in_array($filter->operator, [Operator::NEQ, Operator::NOT_IN], true)
            ) {
                foreach ((array)$filter->value as $v) {
                    $equipmentExcludeIds[] = Uuid::fromString(trim($v))->toBinary();
                }
            } elseif (
                $filter->field === 'wodVariants.wodVariantExercises.exercise.muscles.id' &&
                in_array($filter->operator, [Operator::NEQ, Operator::NOT_IN], true)
            ) {
                foreach ((array)$filter->value as $v) {
                    $muscleExcludeIds[] = Uuid::fromString(trim($v))->toBinary();
                }
            } elseif (
                $filter->field === 'wodVariants.wodVariantExercises.exercise.muscles.muscleGroup.id' &&
                in_array($filter->operator, [Operator::NEQ, Operator::NOT_IN], true)
            ) {
                foreach ((array)$filter->value as $v) {
                    $muscleGroupExcludeIds[] = Uuid::fromString(trim($v))->toBinary();
                }
            } elseif (
                $filter->field === 'wodVariants.wodVariantExercises.exercise.muscles.muscleGroup.muscleArea.id' &&
                in_array($filter->operator, [Operator::NEQ, Operator::NOT_IN], true)
            ) {
                foreach ((array)$filter->value as $v) {
                    $muscleAreaExcludeIds[] = Uuid::fromString(trim($v))->toBinary();
                }
            } elseif (
                $filter->field === 'wodVariants.wodVariantExercises.exercise.muscleSegments.id' &&
                in_array($filter->operator, [Operator::NEQ, Operator::NOT_IN], true)
            ) {
                foreach ((array)$filter->value as $v) {
                    $muscleSegmentExcludeIds[] = Uuid::fromString(trim($v))->toBinary();
                }
            } else {
                $standardFilters[] = $filter;
            }
        }

        $remainingFilters = new FilterCollection($standardFilters);
        if (!$remainingFilters->isEmpty()) {
            $qb = (new DoctrineFilterApplier())->apply($qb, 'w', $remainingFilters);
        }

        if (!empty($exerciseExcludeIds)) {
            $qb->andWhere('NOT EXISTS (
                    SELECT 1 FROM ' . WodVariantExercise::class . ' wve_excl
                    JOIN wve_excl.wodVariant wv_excl
                    WHERE wv_excl.wod = w
                    AND wve_excl.exercise IN (:excl_exercise_ids)
                )')
                ->setParameter('excl_exercise_ids', $exerciseExcludeIds);
        }

        if (!empty($exerciseCategoryExcludeIds)) {
            $qb->andWhere('NOT EXISTS (
                    SELECT 1 FROM ' . WodVariantExercise::class . ' wve_excl_cat
                    JOIN wve_excl_cat.wodVariant wv_excl_cat
                    JOIN wve_excl_cat.exercise ex_excl_cat
                    WHERE wv_excl_cat.wod = w
                    AND ex_excl_cat.exerciseCategory IN (:excl_exercise_category_ids)
                )')
                ->setParameter('excl_exercise_category_ids', $exerciseCategoryExcludeIds);
        }

        if (!empty($equipmentExcludeIds)) {
            $qb->andWhere('NOT EXISTS (
                    SELECT 1 FROM ' . WodVariantExercise::class . ' wve_excl_eq
                    JOIN wve_excl_eq.wodVariant wv_excl_eq
                    JOIN wve_excl_eq.exercise ex_excl_eq
                    WHERE wv_excl_eq.wod = w
                    AND ex_excl_eq.equipment IN (:excl_equipment_ids)
                )')
                ->setParameter('excl_equipment_ids', $equipmentExcludeIds);
        }

        if (!empty($muscleExcludeIds)) {
            $qb->andWhere('NOT EXISTS (
                    SELECT 1 FROM ' . WodVariantExercise::class . ' wve_excl_m
                    JOIN wve_excl_m.wodVariant wv_excl_m
                    JOIN wve_excl_m.exercise ex_excl_m
                    JOIN ex_excl_m.muscles m_excl
                    WHERE wv_excl_m.wod = w
                    AND m_excl.id IN (:excl_muscle_ids)
                )')
                ->setParameter('excl_muscle_ids', $muscleExcludeIds);
        }

        if (!empty($muscleGroupExcludeIds)) {
            $qb->andWhere('NOT EXISTS (
                    SELECT 1 FROM ' . WodVariantExercise::class . ' wve_excl_mg
                    JOIN wve_excl_mg.wodVariant wv_excl_mg
                    JOIN wve_excl_mg.exercise ex_excl_mg
                    JOIN ex_excl_mg.muscles m_excl_mg
                    JOIN m_excl_mg.muscleGroup mg_excl
                    WHERE wv_excl_mg.wod = w
                    AND mg_excl.id IN (:excl_muscle_group_ids)
                )')
                ->setParameter('excl_muscle_group_ids', $muscleGroupExcludeIds);
        }

        if (!empty($muscleAreaExcludeIds)) {
            $qb->andWhere('NOT EXISTS (
                    SELECT 1 FROM ' . WodVariantExercise::class . ' wve_excl_ma
                    JOIN wve_excl_ma.wodVariant wv_excl_ma
                    JOIN wve_excl_ma.exercise ex_excl_ma
                    JOIN ex_excl_ma.muscles m_excl_ma
                    JOIN m_excl_ma.muscleArea ma_excl
                    WHERE wv_excl_ma.wod = w
                    AND ma_excl.id IN (:excl_muscle_area_ids)
                )')
                ->setParameter('excl_muscle_area_ids', $muscleAreaExcludeIds);
        }

        if (!empty($muscleSegmentExcludeIds)) {
            $qb->andWhere('NOT EXISTS (
                    SELECT 1 FROM ' . WodVariantExercise::class . ' wve_excl_ms
                    JOIN wve_excl_ms.wodVariant wv_excl_ms
                    JOIN wve_excl_ms.exercise ex_excl_ms
                    JOIN ex_excl_ms.muscleSegments ms_excl
                    WHERE wv_excl_ms.wod = w
                    AND ms_excl.id IN (:excl_muscle_segment_ids)
                )')
                ->setParameter('excl_muscle_segment_ids', $muscleSegmentExcludeIds);
        }

        // Sort
        // -------------------------------------------------------------------------------------------------------------
        if (!$sorts->isEmpty()) {
            $qb = (new DoctrineSortApplier())
                ->apply($qb, 'w', $sorts);
        }

        // Pagination
        // -------------------------------------------------------------------------------------------------------------
        $aggQb     = clone $qb;
        $aggResult = $aggQb
            ->select('COUNT(DISTINCT w.id) as count')
            ->resetDQLPart('orderBy')
            ->getQuery()
            ->getSingleResult();

        $count = (int)$aggResult['count'];

        $items = $qb
            ->addGroupBy('w.id')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return new LightPaginator(
            $items,
            $count,
            $page,
            $limit,
        );
    }

    public function countWods(): int
    {
        return (int)$this->createQueryBuilder('w')
            ->select('COUNT(w.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countByWodCategorySlug(string $wodCategorySlug): int
    {
        return (int)$this->createQueryBuilder('w')
            ->join('w.wodCategory', 'wc')
            ->select('COUNT(w.id)')
            ->where('wc.slug = :slug')
            ->setParameter('slug', $wodCategorySlug)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countByName(string $name): int
    {
        return (int)$this->createQueryBuilder('w')
            ->select('COUNT(w.id)')
            ->where('w.name LIKE :name')
            ->setParameter('name', $name . '%')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
