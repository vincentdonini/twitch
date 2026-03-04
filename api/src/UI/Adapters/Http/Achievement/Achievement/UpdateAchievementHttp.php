<?php

namespace App\UI\Adapters\Http\Achievement\Achievement;

use App\Domain\Achievement\Achievement\UpdateAchievementDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UpdateAchievementHttp implements UpdateAchievementDTOInterface
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

    public function getAchievementGroupId(): ?Uuid
    {
        return isset($this->payload['achievementGroupId'])
            ? Uuid::fromString($this->payload['achievementGroupId'])
            : null;
    }

    public function getContents(): array
    {
        return $this->payload['contents'] ?? [];
    }
}
