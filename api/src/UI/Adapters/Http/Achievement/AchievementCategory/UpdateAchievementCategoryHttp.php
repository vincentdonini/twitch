<?php

namespace App\UI\Adapters\Http\Achievement\AchievementCategory;

use App\Domain\Achievement\AchievementCategory\UpdateAchievementCategoryDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UpdateAchievementCategoryHttp implements UpdateAchievementCategoryDTOInterface
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

    public function getContents(): array
    {
        return $this->payload['contents'] ?? [];
    }
}
