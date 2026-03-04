<?php

namespace App\UI\Adapters\Http\Achievement\AchievementGroup;

use App\Domain\Achievement\AchievementGroup\CreateAchievementGroupDTOInterface;
use Symfony\Component\Uid\Uuid;

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

    public function getAchievementCategoryId(): ?Uuid
    {
        return isset($this->payload['achievementCategoryId'])
            ? Uuid::fromString($this->payload['achievementCategoryId'])
            : null;
    }
}
