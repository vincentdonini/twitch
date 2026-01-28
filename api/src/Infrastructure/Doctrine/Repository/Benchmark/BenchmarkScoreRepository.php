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
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class BenchmarkScoreRepository extends AbstractEntityRepository implements BenchmarkScoreDALInterface
{
    use LocaleTrait;

    public function __construct(
        protected ManagerRegistry     $registry,
        private readonly RequestStack $requestStack,
        private readonly string       $locale,
        private readonly Security     $security,
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
        ?User             $user = null,
    ): LightPaginator {
        $currentUser = $this->security->getUser();

        $qb = $this->createQueryBuilder('bs');

        // Visibility rules
        // -------------------------------------------------------------------------------------------------------------
        if ($user) {
            $qb
                ->andWhere('bs.user = :userId')
                ->setParameter('userId', hex2bin(str_replace('-', '', (string)$user->getId())));
        }

        if ($currentUser !== $user) {
            $qb->andWhere('bs.private = false');
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
