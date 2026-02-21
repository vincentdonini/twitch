<?php

namespace App\Domain\Achievement\AchievementGroup;

use Symfony\Component\Uid\Uuid;

interface UpsertContentAchievementGroupBulkDTOInterface
{
    public function getId(): Uuid;

    public function getContents(): array;
}
