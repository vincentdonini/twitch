<?php

namespace App\Infrastructure\Doctrine\Repository\Muscle;

use App\Domain\Muscle\Entity\MuscleSegment;
use App\Domain\Muscle\Ports\MuscleSegmentDALInterface;
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
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Uid\Uuid;

class MuscleSegmentRepository extends AbstractEntityRepository implements MuscleSegmentDALInterface
{
    use LocaleTrait;

    public function __construct(
        private readonly string       $locale,
        protected ManagerRegistry     $em,
        private readonly RequestStack $requestStack,
    ) {
        parent::__construct($em);
    }

    public function getClass(): string
    {
        return MuscleSegment::class;
    }

    public function getManager(): string
    {
        return MuscleSegment::class;
    }

    public function getById(Uuid $id): ?MuscleSegment
    {
        return $this->find($id);
    }

    public function listMuscleSegments(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null,
    ): LightPaginator {
        $qb = $this->createQueryBuilder('ms');

        if (!$filters->isEmpty()) {
            (new DoctrineFilterApplier())->apply($qb, 'ms', $filters);
        }

        if (!$sorts->isEmpty()) {
            (new DoctrineSortApplier())->apply($qb, 'ms', $sorts);
        }

        $aggQb     = clone $qb;
        $aggResult = $aggQb
            ->select('COUNT(DISTINCT ms.id) as count')
            ->resetDQLPart('orderBy')
            ->getQuery()
            ->getSingleResult();

        $count = (int)$aggResult['count'];

        $items = $qb
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return new LightPaginator($items, $count, $page, $limit);
    }

    /** @return MuscleSegment[] */
    public function getByMuscleId(Uuid $muscleId): array
    {
        return $this->createQueryBuilder('ms')
            ->where('ms.muscle = :muscle')
            ->setParameter('muscle', $muscleId, UuidType::NAME)
            ->getQuery()
            ->getResult();
    }

    /** @return array<string, int> */
    public function getWodCounts(): array
    {
        $rows = $this->getEntityManager()->createQuery('
            SELECT ms.id as id, COUNT(DISTINCT w.id) as cnt
            FROM ' . Wod::class . ' w
            JOIN w.wodVariants wv
            JOIN wv.wodVariantExercises wve
            JOIN wve.exercise e
            JOIN e.muscleSegments ms
            GROUP BY ms.id
        ')->getResult();

        $counts = [];
        foreach ($rows as $row) {
            $counts[(string)$row['id']] = (int)$row['cnt'];
        }
        return $counts;
    }
}
