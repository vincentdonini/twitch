<?php

namespace App\Domain\Achievement\AchievementCategory;

use Symfony\Component\Uid\Uuid;

interface UpdateAchievementCategoryDTOInterface
{
    public function getId(): Uuid;

    public function getCode(): ?string;

    public function getPosition(): ?int;

    public function getContents(): array;
}
