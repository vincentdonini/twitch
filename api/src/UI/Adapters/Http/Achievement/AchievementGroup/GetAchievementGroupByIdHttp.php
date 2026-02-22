<?php

namespace App\UI\Adapters\Http\Achievement\AchievementGroup;

use App\Domain\Achievement\AchievementGroup\GetAchievementGroupByIdDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class GetAchievementGroupByIdHttp implements GetAchievementGroupByIdDTOInterface
{
    public function __construct(
        private Uuid $id,
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }
}
