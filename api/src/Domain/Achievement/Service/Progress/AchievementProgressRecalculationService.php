<?php

namespace App\Domain\Achievement\Service\Progress;

use App\Domain\Achievement\Entity\Achievement;
use App\Domain\Achievement\Ports\AchievementDALInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\Ports\UserDALInterface;
use Doctrine\ORM\EntityManagerInterface;

final readonly class AchievementProgressRecalculationService
{
    public function __construct(
        private EntityManagerInterface     $em,
        private UserDALInterface           $userDAL,
        private AchievementDALInterface    $achievementDAL,
        private AchievementProgressUpdater $updater
    ) {
    }

    public function recalculate(
        ?User        $user = null,
        ?Achievement $achievement = null
    ): void {
        $users        = $user ? [$user] : $this->userDAL->findAll();
        $achievements = $achievement ? [$achievement] : $this->achievementDAL->findAll();

        foreach ($users as $user) {
            foreach ($achievements as $achievement) {
                $this->updater->update($user, $achievement);
            }
        }

        $this->em->flush();
    }
}
