<?php

namespace App\UI\Adapters\Http\Achievement\AchievementCategory;

use App\Domain\Achievement\AchievementCategory\GetAchievementCategoryByIdDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class GetAchievementCategoryByIdHttp implements GetAchievementCategoryByIdDTOInterface
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
