<?php

namespace App\Domain\Achievement\Achievement;

use App\Domain\Achievement\Enum\AchievementSourceEnum;
use Symfony\Component\Uid\Uuid;

interface CreateAchievementDTOInterface
{
    public function getCode(): ?string;

    public function getPosition(): ?int;

    public function getSource(): ?AchievementSourceEnum;

    public function getAchievementGroupId(): ?Uuid;
}
