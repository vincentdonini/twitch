<?php

namespace App\Domain\Achievement\AchievementGroup;

use Symfony\Component\Uid\Uuid;

interface UpdateAchievementGroupDTOInterface
{
    public function getId(): Uuid;

    public function getCode(): ?string;

    public function getPosition(): ?int;

    public function getAchievementCategoryId(): ?Uuid;

    public function getContents(): array;
}
