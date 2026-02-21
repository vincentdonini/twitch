<?php

namespace App\Domain\Achievement\Service\Progress;


use App\Domain\Achievement\Entity\AchievementLevel;
use App\Domain\Achievement\Enum\AchievementSourceEnum;
use App\Domain\User\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.achievement_progress_calculator')]
interface AchievementProgressCalculatorInterface
{
    public function supports(AchievementSourceEnum $source, AchievementLevel $achievementLevel): bool;

    public function calculate(User $user, AchievementSourceEnum $source, AchievementLevel $achievementLevel): float;
}
