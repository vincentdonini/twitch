<?php

namespace App\Domain\Achievement\Achievement;

use Symfony\Component\Uid\Uuid;

interface UpdateAchievementDTOInterface
{
    public function getId(): Uuid;

    public function getCode(): ?string;

    public function getPosition(): ?int;

    public function getAchievementGroupId(): ?Uuid;

    public function getContents(): array;
}
