<?php

namespace App\Domain\Achievement\Service\Progress;

use App\Domain\Achievement\Entity\Achievement;
use App\Domain\Achievement\Entity\AchievementLevel;
use App\Domain\Achievement\Entity\UserAchievementProgress;
use App\Domain\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final readonly class AchievementProgressUpdater
{
    public function __construct(
        private CalculatorRegistry     $calculatorRegistry,
        private EntityManagerInterface $em
    ) {
    }

    public function update(User $user, Achievement $achievement): void
    {
        /* @var AchievementLevel $achievementLevel */
        foreach ($achievement->getAchievementLevels() as $achievementLevel) {

            $calculator  = $this->calculatorRegistry->getCalculator($achievement->getSource(), $achievementLevel);
            $progression = $calculator->calculate($user, $achievement->getSource(), $achievementLevel);

            /* @var UserAchievementProgress $userAchievementProgress */
            $userAchievementProgress = $this->em->getRepository(UserAchievementProgress::class)
                ->findOneBy([
                    'user'             => $user,
                    'achievement'      => $achievement,
                    'achievementLevel' => $achievementLevel,
                ]);

            if (!$userAchievementProgress) {
                $userAchievementProgress = new UserAchievementProgress(
                    $user,
                    $achievement,
                    $achievementLevel
                );
                $this->em->persist($userAchievementProgress);
            }

            $userAchievementProgress->updateProgress($progression);
        }
    }
}
