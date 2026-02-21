<?php

namespace App\UI\Adapters\Http\Achievement\AchievementGroup;

use App\Domain\Achievement\AchievementGroup\CreateAchievementGroupDTOInterface;

final readonly class CreateAchievementGroupHttp implements CreateAchievementGroupDTOInterface
{
    public function __construct(
        private array $payload,
    ) {

    }

    public function getCode(): ?string
    {
        return $this->payload['code'] ?? null;
    }

    public function getPosition(): ?int
    {
        return $this->payload['position'] ?? null;
    }

    public function getAchievementCategoryId(): ?string
    {
        return $this->payload['achievementCategoryId'] ?? null;
    }
}
