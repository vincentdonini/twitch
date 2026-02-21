<?php

namespace App\Infrastructure\Doctrine\Repository\Wod;

use App\Domain\User\Entity\User;
use App\Domain\Wod\Entity\WodScore;
use App\Domain\Wod\Leaderboard\LeaderboardOrdering;
use App\Domain\Wod\Ports\WodScoreDALInterface;
use App\Infrastructure\Doctrine\Filters\DoctrineFilterApplier;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Doctrine\Repository\AbstractEntityRepository;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Uid\Uuid;

class WodScoreRepository extends AbstractEntityRepository implements WodScoreDALInterface
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
        return WodScore::class;
    }

    public function getManager(): string
    {
        return WodScore::class;
    }

    public function getById(string $id): ?WodScore
    {
        return $this->createQueryBuilder('ws')
            ->where('ws.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getByUser(
        User   $user,
        string $order = 'DESC'
    ): array {
        return $this->createQueryBuilder('ws')
            ->select('ws.performedAt')
            ->where('ws.user = :user')
            ->setParameter('user', $user)
            ->orderBy('ws.performedAt', $order)
            ->getQuery()
            ->getResult();
    }

    public function listWodScores(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?User             $currentUser = null,
    ): LightPaginator {
        $qb = $this->createQueryBuilder('ws');

        // Visibility rules
        // -------------------------------------------------------------------------------------------------------------
        if ($currentUser === null) {
            $qb->andWhere('ws.private = false');
        } elseif (
            $currentUser->hasRole('ROLE_ADMIN') ||
            $currentUser->hasRole('ROLE_COACH')
        ) {
            // Admin or Coach → full access
            // No restrictions
        } else {
            // User connecté standard
            $qb
                ->andWhere('ws.private = false OR ws.user = :currentUser')
                ->setParameter('currentUser', $currentUser);
        }

        // Filters
        // -------------------------------------------------------------------------------------------------------------
        if (!$filters->isEmpty()) {
            $qb = (new DoctrineFilterApplier())
                ->apply($qb, 'ws', $filters);
        }

        // Pagination
        // -------------------------------------------------------------------------------------------------------------
        $aggQb     = clone $qb;
        $aggResult = $aggQb
            ->select('COUNT(ws.id) as count')
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

    public function getLeaderboard(
        string              $wodId,
        string              $wodDivisionId,
        string              $gender,
        LeaderboardOrdering $ordering,
        int                 $page = 1,
        int                 $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection   $filters = null,
        ?User               $currentUser = null,
    ): LightPaginator {
        $qb = $this->createQueryBuilder('ws')
            ->innerJoin('ws.wod', 'w')
            ->where('w.id = :wodId')
            ->setParameter('wodId', $wodId);

        // Visibility rules
        // -------------------------------------------------------------------------------------------------------------
        if ($currentUser === null) {
            // Non connecté → uniquement public
            $qb->andWhere('ws.private = false');
        } else {
            // Utilisateur connecté standard
            $qb
                ->andWhere('ws.private = false OR ws.user = :currentUser')
                ->setParameter('currentUser', $currentUser);
        }

        $qb
            ->join('ws.wodVariant', 'wv')
            ->join('wv.wodDivision', 'wd')
            ->andWhere('wd.id = :wodDivisionId')
            ->andWhere('wv.gender = :gender')
            ->setParameter('wodDivisionId', $wodDivisionId)
            ->setParameter('gender', $gender);

        // Filters
        // -------------------------------------------------------------------------------------------------------------
//        if (!$filters->isEmpty()) {
//            (new DoctrineFilterApplier())->apply($qb, 'ws', $filters);
//        }

        // Sort
        // -------------------------------------------------------------------------------------------------------------
        foreach ($ordering->all() as $order) {
            $qb->addOrderBy(
                'ws.' . $order['field'],
                $order['direction']
            );
        }

        // Pagination
        // -------------------------------------------------------------------------------------------------------------
        $aggQb     = clone $qb;
        $aggResult = $aggQb
            ->select('COUNT(ws.id) as count')
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
            $limit
        );
    }

    public function getByUserAndName(User $user, string $name): array
    {
        return $this->createQueryBuilder('ws')
            ->join('ws.wod', 'w')
            ->where('ws.user = :user')
            ->andWhere('w.name = :name')
            ->setParameter('user', $user->getId(), UuidType::NAME)
            ->setParameter('name', $name)
            ->getQuery()
            ->getResult();
    }

    public function hasScaledToRx(User $user): bool
    {
        $scores = $this->findBy(['user' => $user]);

        $wods = [];

        foreach ($scores as $score) {
            $wodId    = $score->getWod()->getId();
            $isScaled = $score->getWodVariant()->isScaled();

            $wods[$wodId][$isScaled ? 'scaled' : 'rx'] = true;

            if (
                !empty($wods[$wodId]['scaled']) &&
                !empty($wods[$wodId]['rx'])
            ) {
                return true;
            }
        }

        return false;
    }

    public function countWeekEndWods(User $user): int
    {
        $scores = $this->findBy(['user' => $user]);

        $count = 0;

        foreach ($scores as $score) {
            $dayOfWeek = (int)$score->getPerformedAt()->format('N'); // 6 = samedi, 7 = dimanche

            if ($dayOfWeek >= 6) {
                $count++;
            }
        }

        return $count;
    }

    public function countByUserAndName(User $user, string $name): int
    {
        return $this->createQueryBuilder('ws')
            ->select('COUNT(ws.id)')
            ->join('ws.wod', 'w')
            ->where('ws.user = :user')
            ->andWhere('w.name = :name')
            ->setParameter('user', $user->getId(), UuidType::NAME)
            ->setParameter('name', $name)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countTeamScoresByUser(User $user): int
    {
        return (int)$this->createQueryBuilder('ws')
            ->select('COUNT(ws.id)')
            ->join('ws.wod', 'w')
            ->where('ws.user = :user')
            ->andWhere('w.teamSize > 1')
            ->setParameter('user', $user->getId(), UuidType::NAME)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countDistinctByUserAndWodCategorySlug(User $user, string $wodCategorySlug): int
    {
        return (int)$this->createQueryBuilder('ws')
            ->select('COUNT(DISTINCT w.id)')
            ->join('ws.wod', 'w')
            ->join('w.wodCategory', 'wc')
            ->where('ws.user = :user')
            ->andWhere('wc.slug = :slug')
            ->setParameter('user', $user->getId(), UuidType::NAME)
            ->setParameter('slug', $wodCategorySlug)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countDistinctByUserAndName(User $user, string $name): int
    {
        return (int)$this->createQueryBuilder('ws')
            ->select('COUNT(w.id)')
            ->join('ws.wod', 'w')
            ->where('ws.user = :user')
            ->andWhere('w.name LIKE :name')
            ->setParameter('user', $user->getId(), UuidType::NAME)
            ->setParameter('name', $name . '%')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
