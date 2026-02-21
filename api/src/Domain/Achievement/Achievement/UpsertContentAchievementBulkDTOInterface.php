<?php

namespace App\Domain\Achievement\Achievement;

use Symfony\Component\Uid\Uuid;

interface UpsertContentAchievementBulkDTOInterface
{
    public function getId(): Uuid;

    public function getContents(): array;
}
