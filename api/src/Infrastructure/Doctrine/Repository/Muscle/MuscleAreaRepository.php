<?php

namespace App\Infrastructure\Doctrine\Repository\Muscle;

use App\Domain\Muscle\Entity\MuscleArea;
use App\Domain\Muscle\Ports\MuscleAreaDALInterface;
use App\Domain\Wod\Entity\Wod;
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

class MuscleAreaRepository extends AbstractEntityRepository implements MuscleAreaDALInterface
{
    use LocaleTrait;

    public function __construct(
        private readonly string       $locale,
        protected ManagerRegistry     $registry,
        private readonly RequestStack $requestStack,
    ) {
        parent::__construct($registry);
    }

    public function getClass(): string
    {
        return MuscleArea::class;
    }

    public function getManager(): string
    {
        return MuscleArea::class;
    }

    public function getById(Uuid $id): ?MuscleArea
    {
        return $this->find($id);
    }

    public function listMuscleAreas(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null,
    ): LightPaginator {
        $qb = $this->createQueryBuilder('ma');

        // Filters
        // -------------------------------------------------------------------------------------------------------------
        if (!$filters->isEmpty()) {
            (new DoctrineFilterApplier())->apply($qb, 'ma', $filters);
        }

        // Sort
        // -------------------------------------------------------------------------------------------------------------
        if (!$sorts->isEmpty()) {
            (new DoctrineSortApplier())->apply($qb, 'ma', $sorts);
        }

        // Pagination
        // -------------------------------------------------------------------------------------------------------------
        $aggQb     = clone $qb;
        $aggResult = $aggQb
            ->select('COUNT(DISTINCT ma.id) as count')
            ->resetDQLPart('orderBy')
            ->getQuery()
            ->getSingleResult();

        $count = (int)$aggResult['count'];

        $items = $qb
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

    /** @return array<string, int> */
    public function getWodCounts(): array
    {
        $rows = $this->getEntityManager()->createQuery('
            SELECT ma.id as id, COUNT(DISTINCT w.id) as cnt
            FROM ' . Wod::class . ' w
            JOIN w.wodVariants wv
            JOIN wv.wodVariantExercises wve
            JOIN wve.exercise e
            JOIN e.muscles m
            JOIN m.muscleArea ma
            GROUP BY ma.id
        ')->getResult();

        $counts = [];
        foreach ($rows as $row) {
            $counts[(string)$row['id']] = (int)$row['cnt'];
        }
        return $counts;
    }
}
