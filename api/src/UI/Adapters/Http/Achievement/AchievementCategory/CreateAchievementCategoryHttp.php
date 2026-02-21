<?php

namespace App\UI\Adapters\Http\Achievement\AchievementCategory;

use App\Domain\Achievement\AchievementCategory\CreateAchievementCategoryDTOInterface;

final readonly class CreateAchievementCategoryHttp implements CreateAchievementCategoryDTOInterface
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
}
