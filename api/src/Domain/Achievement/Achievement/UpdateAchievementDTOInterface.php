<?php

namespace App\Domain\Achievement\Achievement;

use Symfony\Component\Uid\Uuid;

interface UpdateAchievementDTOInterface
{
    public function getId(): Uuid;

    public function getCode(): ?string;

    public function getPosition(): ?string;

    public function getAchievementGroupId(): ?Uuid;

    public function getContents(): array;
}
