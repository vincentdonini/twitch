<?php

namespace App\Infrastructure\Doctrine\Repository\Achievement;

use App\Domain\Achievement\Entity\Achievement;
use App\Domain\Achievement\Entity\AchievementLevel;
use App\Domain\Achievement\Entity\UserAchievementProgress;
use App\Domain\Achievement\Ports\UserAchievementProgressDALInterface;
use App\Domain\User\Entity\User;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Doctrine\Repository\AbstractEntityRepository;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Uid\Uuid;

class UserAchievementProgressRepository extends AbstractEntityRepository implements UserAchievementProgressDALInterface
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
        return UserAchievementProgress::class;
    }

    public function getManager(): string
    {
        return UserAchievementProgress::class;
    }

    public function getById(Uuid $id): ?UserAchievementProgress
    {
        return $this->find($id);
    }

    public function listUserAchievementProgresses(
        int   $page = 1,
        int   $limit = 10,
        ?User $user = null,
    ): LightPaginator {
        $qb = $this->createQueryBuilder('uap');

        if ($user) {
            $qb
                ->andWhere('uap.user = :userId')
                ->setParameter('userId', hex2bin(str_replace('-', '', (string)$user->getId())));
        }

        // Pagination
        // -------------------------------------------------------------------------------------------------------------
        $aggQb     = clone $qb;
        $aggResult = $aggQb
            ->select('COUNT(uap.user) as count')
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

    /** @return UserAchievementProgress[] */
    public function findByUsers(User $user, User $comparedUser): array
    {
        return $this->createQueryBuilder('uap')
            ->join('uap.achievement', 'a')
            ->addSelect('a')
            ->where('uap.user IN (:users)')
            ->setParameter(
                'users',
                [
                    hex2bin(str_replace('-', '', (string)$user->getId())),
                    hex2bin(str_replace('-', '', (string)$comparedUser->getId())),
                ]
            )
            ->getQuery()
            ->getResult();
    }

    public function findUserAchievementProgress(
        User             $user,
        Achievement      $achievement,
        AchievementLevel $achievementLevel
    ): ?UserAchievementProgress {
        return $this->findOneBy([
            'user'             => $user,
            'achievement'      => $achievement,
            'achievementLevel' => $achievementLevel,
        ]);
    }

    public function countByAchievementLevel(AchievementLevel $achievementLevel): int
    {
        return (int)$this->createQueryBuilder('uap')
            ->select('COUNT(DISTINCT uap.user)')
            ->where('uap.achievementLevel = :achievementLevel')
            ->andWhere('uap.completed = true')
            ->setParameter('achievementLevel', $achievementLevel)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countGroupedByAchievementLevel(): array
    {
        return $this->createQueryBuilder('uap')
            ->select('IDENTITY(uap.achievementLevel) as levelId')
            ->addSelect('COUNT(DISTINCT uap.user) as total')
            ->where('uap.completed = true')
            ->groupBy('uap.achievementLevel')
            ->getQuery()
            ->getArrayResult();
    }
}
