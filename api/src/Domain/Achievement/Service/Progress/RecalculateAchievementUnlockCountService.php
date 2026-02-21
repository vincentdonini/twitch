<?php

namespace App\Domain\Achievement\Service\Progress;

use App\Domain\Achievement\Entity\Achievement;
use App\Domain\Achievement\Entity\AchievementLevel;
use App\Domain\Achievement\Ports\AchievementDALInterface;
use App\Domain\Achievement\Ports\UserAchievementProgressDALInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

final readonly class RecalculateAchievementUnlockCountService
{
    public function __construct(
        private EntityManagerInterface              $em,
        private AchievementDALInterface             $achievementDAL,
        private UserAchievementProgressDALInterface $userAchievementProgressDAL,
    ) {
    }

    public function recalculate(?Achievement $achievement = null): void
    {
        if ($achievement) {
            $this->recalculateOne($achievement);
            $this->em->flush();
            return;
        }

        $this->recalculateAll();
        $this->em->flush();
    }

    private function recalculateOne(Achievement $achievement): void
    {
        foreach ($achievement->getAchievementLevels() as $achievementLevel) {
            $count = $this->userAchievementProgressDAL->countByAchievementLevel($achievementLevel);
            $achievementLevel->setUnlockCount($count);
        }
    }

    private function recalculateAll(): void
    {
        $achievements = $this->achievementDAL->findAll();

        $groupedCounts = $this->userAchievementProgressDAL
            ->countGroupedByAchievementLevel();

        $map = [];

        foreach ($groupedCounts as $row) {
            $uuid       = Uuid::fromBinary($row['levelId'])->toRfc4122();
            $map[$uuid] = (int)$row['total'];
        }

        /* @var Achievement $achievement */
        foreach ($achievements as $achievement) {
            /* @var AchievementLevel $achievementLevel */
            foreach ($achievement->getAchievementLevels() as $achievementLevel) {
                $achievementLevelId = $achievementLevel->getId()->toRfc4122();
                $count              = $map[$achievementLevelId] ?? 0;

                $achievementLevel->setUnlockCount($count);
            }
        }
    }
}
