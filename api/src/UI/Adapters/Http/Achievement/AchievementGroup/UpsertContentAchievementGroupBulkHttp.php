<?php

namespace App\UI\Adapters\Http\Achievement\AchievementGroup;

use App\Domain\Achievement\AchievementGroup\UpsertContentAchievementGroupBulkDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UpsertContentAchievementGroupBulkHttp implements UpsertContentAchievementGroupBulkDTOInterface
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

    public function getContents(): array
    {
        return $this->payload;
    }
}