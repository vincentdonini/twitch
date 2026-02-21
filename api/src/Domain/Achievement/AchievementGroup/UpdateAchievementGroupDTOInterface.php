<?php

namespace App\Domain\Achievement\AchievementGroup;

use Symfony\Component\Uid\Uuid;

interface UpdateAchievementGroupDTOInterface
{
    public function getId(): Uuid;

    public function getCode(): ?string;

    public function getPosition(): ?string;

    public function getAchievementCategoryId(): ?string;

    public function getContents(): array;
}
