<?php

namespace App\Domain\Achievement\Achievement;

use Symfony\Component\Uid\Uuid;

interface GetAchievementByIdDTOInterface
{
    public function getId(): Uuid;
}
