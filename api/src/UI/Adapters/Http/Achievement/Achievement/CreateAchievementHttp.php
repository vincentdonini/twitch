<?php

namespace App\UI\Adapters\Http\Achievement\Achievement;

use App\Domain\Achievement\Achievement\CreateAchievementDTOInterface;
use App\Domain\Achievement\Enum\AchievementSourceEnum;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

final readonly class CreateAchievementHttp implements CreateAchievementDTOInterface
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

    public function getSource(): ?AchievementSourceEnum
    {
        $source = $this->payload['source'] ?? null;

        if ($source === null) {
            return null;
        }

        return AchievementSourceEnum::tryFrom($source)
            ?? throw new InvalidArgumentException(sprintf('Invalid source "%s".', $source));
    }

    public function getAchievementGroupId(): ?Uuid
    {
        return isset($this->payload['achievementGroupId'])
            ? Uuid::fromString($this->payload['achievementGroupId'])
            : null;
    }
}
