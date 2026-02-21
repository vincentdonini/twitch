<?php

namespace App\Domain\Achievement\Achievement;

use Symfony\Component\Uid\Uuid;

interface UpsertContentAchievementDTOInterface
{
    public function getId(): Uuid;

    public function getLocale(): string;

    public function getTitle(): ?string;

    public function getDescription(): ?string;
}
