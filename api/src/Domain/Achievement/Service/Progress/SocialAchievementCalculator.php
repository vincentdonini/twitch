<?php

namespace App\Domain\Achievement\Service\Progress;

use App\Domain\Achievement\Entity\AchievementLevel;
use App\Domain\Achievement\Enum\AchievementLevelEnum;
use App\Domain\Achievement\Enum\AchievementSourceEnum;
use App\Domain\User\Entity\User;

class SocialAchievementCalculator implements AchievementProgressCalculatorInterface
{
    public function supports(AchievementSourceEnum $source, AchievementLevel $achievementLevel): bool
    {
        return $source === AchievementSourceEnum::tryFrom('social');
    }

    public function calculate(User $user, AchievementSourceEnum $source, AchievementLevel $achievementLevel): float
    {
        $achievementCode = $achievementLevel->getAchievement()->getCode();
        $level           = $achievementLevel->getLevel();

        $percentageDone = $this->getPercentageDone($user, $achievementCode, $level);

        return min($percentageDone * 100, 100);
    }

    private function getPercentageDone(User $user, string $achievementCode, AchievementLevelEnum $level): float
    {
        return match ($achievementCode) {

            // ---------------------------------------------------------------------------------------------------------
            // SOCIAL
            // ---------------------------------------------------------------------------------------------------------

            // --- SOCIAL ----------------------------------------------------------------------------------------------
            'CHEERLEADER'       => $this->toImplement(),
            'COACHS_FAVORITE'   => $this->toImplement(),
            'BOX_REGULAR'       => $this->toImplement(),
            'BOX_LEGEND'        => $this->toImplement(),
            'HIGH_FIVE_HERO'    => $this->toImplement(),
            'REFERRAL_STAR'     => $this->toImplement(),
            'RISING_STAR'       => $this->toImplement(),
            'SPIRIT_OF_THE_BOX' => $this->toImplement(),
            'WALL_OF_FAME'      => $this->toImplement(),

            default             => throw new \LogicException(
                sprintf(
                    'Unsupported achievement "%s"',
                    $achievementCode
                )
            ),
        };
    }

    private function toImplement(): float
    {
        return 0.0;
    }
}
