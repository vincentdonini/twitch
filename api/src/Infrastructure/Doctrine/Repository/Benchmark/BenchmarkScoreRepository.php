<?php

namespace App\Infrastructure\Doctrine\Repository\Benchmark;

use App\Domain\Benchmark\Entity\BenchmarkScore;
use App\Domain\Benchmark\Ports\BenchmarkScoreDALInterface;
use App\Domain\User\Entity\User;
use App\Infrastructure\Doctrine\Filters\DoctrineFilterApplier;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Doctrine\Repository\AbstractEntityRepository;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Filters\FilterCollection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
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
        return $this->find($id);
    }

    /* @return BenchmarkScore[] */
    public function getByUser(
        User   $user,
        string $order = 'DESC'
    ): array {
        return $this->createQueryBuilder('bs')
            ->select('bs.performedAt')
            ->where('bs.user = :user')
            ->setParameter('user', $user)
            ->orderBy('bs.performedAt', $order)
            ->getQuery()
            ->getResult();
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

    public function countDistinctByUser(User $user): int
    {
        return (int)$this->createQueryBuilder('bs')
            ->select('COUNT(DISTINCT b.id)')
            ->join('bs.benchmark', 'b')
            ->where('bs.user = :user')
            ->setParameter('user', $user->getId(), UuidType::NAME)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
