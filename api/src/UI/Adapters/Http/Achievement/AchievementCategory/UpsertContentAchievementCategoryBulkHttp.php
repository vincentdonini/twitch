<?php

namespace App\UI\Adapters\Http\Achievement\AchievementCategory;

use App\Domain\Achievement\AchievementCategory\UpsertContentAchievementCategoryBulkDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UpsertContentAchievementCategoryBulkHttp implements UpsertContentAchievementCategoryBulkDTOInterface
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
