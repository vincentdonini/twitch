<?php

namespace App\UI\Adapters\Http\Achievement\AchievementGroup;

use App\Domain\Achievement\AchievementGroup\GetAchievementGroupByIdDTOInterface;

final readonly class GetAchievementGroupByIdHttp implements GetAchievementGroupByIdDTOInterface
{
    public function __construct(
        private string $id,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }
}
