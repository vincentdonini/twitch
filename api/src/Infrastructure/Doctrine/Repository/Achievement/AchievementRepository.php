<?php

namespace App\Infrastructure\Doctrine\Repository\Achievement;

use App\Domain\Achievement\Entity\Achievement;
use App\Domain\Achievement\Ports\AchievementDALInterface;
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

class AchievementRepository extends AbstractEntityRepository implements AchievementDALInterface
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
        return Achievement::class;
    }

    public function getManager(): string
    {
        return Achievement::class;
    }

    public function getById(Uuid $id): ?Achievement
    {
        return $this->find($id);
    }

    public function listAchievements(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null,
    ): LightPaginator {
        $qb = $this->createQueryBuilder('a');

        // Filters
        // -------------------------------------------------------------------------------------------------------------
        if (!$filters->isEmpty()) {
            $qb = (new DoctrineFilterApplier())
                ->apply($qb, 'a', $filters);
        }

        // Sort
        // -------------------------------------------------------------------------------------------------------------
        if (!$sorts->isEmpty()) {
            $qb = (new DoctrineSortApplier())
                ->apply($qb, 'a', $sorts);
        }

        // Pagination
        // -------------------------------------------------------------------------------------------------------------
        $aggQb     = clone $qb;
        $aggResult = $aggQb
            ->select('COUNT(a.id) as count')
            ->resetDQLPart('orderBy')
            ->getQuery()
            ->getSingleResult();

        $count = (int)$aggResult['count'];

        if ($limit === -1) {
            $items = $qb
                ->getQuery()
                ->getResult();

            return new LightPaginator(
                $items,
                $count,
                1,
                $count
            );
        }

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

    public function findAll(): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
