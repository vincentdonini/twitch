<?php

namespace App\Domain\Achievement\AchievementCategory;

use Symfony\Component\Uid\Uuid;

interface UpsertContentAchievementCategoryBulkDTOInterface
{
    public function getId(): Uuid;

    public function getContents(): array;
}
