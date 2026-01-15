<?php

namespace App\Infrastructure\Doctrine\Repository\Benchmark;

use App\Domain\User\Entity\User;
use App\Domain\Benchmark\Entity\BenchmarkScore;
use App\Domain\Benchmark\Ports\BenchmarkScoreDALInterface;
use App\Infrastructure\Doctrine\Filters\DoctrineFilterApplier;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Doctrine\Repository\AbstractEntityRepository;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Filters\FilterCollection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

class BenchmarkScoreRepository extends AbstractEntityRepository implements BenchmarkScoreDALInterface
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
        return BenchmarkScore::class;
    }

    public function getManager(): string
    {
        return BenchmarkScore::class;
    }

    public function getById(string $id): ?BenchmarkScore
    {
        return $this->createQueryBuilder('bs')
            ->where('bs.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function listBenchmarkScores(
        int               $page = 1,
        int               $limit = 15,
        ?FilterCollection $filters = null,
        ?User             $currentUser = null,
    ): LightPaginator {
        $qb = $this->createQueryBuilder('bs');

        // Visibility rules
        // -------------------------------------------------------------------------------------------------------------
        if ($currentUser === null) {
            $qb->andWhere('bs.private = false');
        } elseif (
            $currentUser->hasRole('ROLE_ADMIN') ||
            $currentUser->hasRole('ROLE_COACH')
        ) {
            // Admin or Coach → full access
            // No restrictions
        } else {
            // User connecté standard
            $qb
                ->andWhere('bs.private = false OR bs.user = :currentUser')
                ->setParameter('currentUser', $currentUser);
        }

        // Filters
        // -------------------------------------------------------------------------------------------------------------
        if (!$filters->isEmpty()) {
            $qb = (new DoctrineFilterApplier())
                ->apply($qb, 'bs', $filters);
        }

        // Pagination
        // -------------------------------------------------------------------------------------------------------------
        $aggQb     = clone $qb;
        $aggResult = $aggQb
            ->select('COUNT(bs.id) as count')
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
}
