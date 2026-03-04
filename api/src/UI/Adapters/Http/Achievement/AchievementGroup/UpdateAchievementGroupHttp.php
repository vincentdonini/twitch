<?php

namespace App\UI\Adapters\Http\Achievement\AchievementGroup;

use App\Domain\Achievement\AchievementGroup\UpdateAchievementGroupDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UpdateAchievementGroupHttp implements UpdateAchievementGroupDTOInterface
{
    public function __construct(
        private Uuid  $id,
        private array $payload,
    ) {

    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->payload['code'] ?? null;
    }

    public function getPosition(): ?int
    {
        return isset($this->payload['position']) ? (int) $this->payload['position'] : null;
    }

    public function getAchievementCategoryId(): ?Uuid
    {
        return isset($this->payload['achievementCategoryId'])
            ? Uuid::fromString($this->payload['achievementCategoryId'])
            : null;
    }

    public function getContents(): array
    {
        return $this->payload['contents'] ?? [];
    }
}
