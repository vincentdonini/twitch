<?php

namespace App\Domain\Achievement\AchievementGroup;

use Symfony\Component\Uid\Uuid;

interface UpsertContentAchievementGroupDTOInterface
{
    public function getId(): Uuid;

    public function getLocale(): string;

    public function getTitle(): ?string;

    public function getDescription(): ?string;
}
