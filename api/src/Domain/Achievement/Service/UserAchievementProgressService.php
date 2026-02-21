<?php

namespace App\Domain\Achievement\Service;

use App\Application\Achievement\DTO\UserAchievementProgressDTO;
use App\Application\Common\CollectionMapper;
use App\Domain\Achievement\Entity\UserAchievementProgress;
use App\Domain\Achievement\Ports\AchievementDALInterface;
use App\Domain\User\Ports\UserDALInterface;
use App\Infrastructure\Doctrine\Repository\Achievement\AchievementRepository;
use App\Infrastructure\Doctrine\Repository\Achievement\UserAchievementProgressRepository;
use App\Infrastructure\Doctrine\Repository\Benchmark\BenchmarkScoreRepository;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Doctrine\Repository\Wod\WodScoreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class UserAchievementProgressService
{
    use LocaleTrait;

    private array $filterMapping;

    public function __construct(
        private RequestStack                      $requestStack,
        private EntityManagerInterface            $em,
        private AchievementRepository             $achievementRepo,
        private UserDALInterface                  $userDAL,
        private AchievementDALInterface           $achievementDAL,
        private UserAchievementProgressRepository $userAchievementProgressRepo,
        private WodScoreRepository                $wodScoreRepo,
        private BenchmarkScoreRepository          $benchmarkScoreRepo
    ) {
    }

    public function transformToDTO(UserAchievementProgress $userAchievementProgress): ?UserAchievementProgressDTO
    {
        return new UserAchievementProgressDTO(
            achievementId   : $userAchievementProgress->getAchievement()->getId(),
            achievementCode : $userAchievementProgress->getAchievement()->getCode(),
            achievementLevel: $userAchievementProgress->getAchievementLevel()->getLevel(),
            progress        : $userAchievementProgress->getProgress(),
            completed       : $userAchievementProgress->isCompleted(),
            completedAt     : $userAchievementProgress->getCompletedAt()
        );
    }

    public function transformCollectionToDTO(array $userAchievementProgresses): array
    {
        return CollectionMapper::mapAndFilter(
            $userAchievementProgresses,
            fn(UserAchievementProgress $userAchievementProgress) => $this->transformToDTO($userAchievementProgress)
        );
    }
}
