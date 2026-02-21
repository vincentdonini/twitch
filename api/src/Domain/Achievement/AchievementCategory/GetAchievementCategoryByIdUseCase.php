<?php

namespace App\Domain\Achievement\AchievementCategory;

use App\Domain\Achievement\Entity\AchievementCategory;
use App\Domain\Achievement\Ports\AchievementCategoryDALInterface;
use Doctrine\ORM\EntityNotFoundException;

final readonly class GetAchievementCategoryByIdUseCase
{
    public function __construct(
        private AchievementCategoryDALInterface $achievementCategoryDAL,
    ) {
    }

    public function execute(GetAchievementCategoryByIdDTOInterface $dto): AchievementCategory
    {
        $achievementCategory = $this->achievementCategoryDAL->getById($dto->getId());
        if (!$achievementCategory instanceof AchievementCategory) {
            throw new EntityNotFoundException();
        }

        return $achievementCategory;
    }
}
