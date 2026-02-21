<?php

namespace App\Domain\Achievement\AchievementCategory;

use App\Domain\Achievement\Entity\AchievementCategory;
use App\Domain\Achievement\Ports\AchievementCategoryDALInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityNotFoundException;

final readonly class GetAchievementCategoryContentsByIdUseCase
{
    public function __construct(
        private AchievementCategoryDALInterface $achievementCategoryDAL,
    ) {
    }

    public function execute(GetAchievementCategoryByIdDTOInterface $dto): Collection
    {
        $achievementCategory = $this->achievementCategoryDAL->getById($dto->getId());
        if (!$achievementCategory instanceof AchievementCategory) {
            throw new EntityNotFoundException();
        }

        return $achievementCategory->getContents();
    }
}

