<?php

namespace App\Domain\Achievement\AchievementGroup;

use App\Domain\Achievement\Entity\AchievementGroup;
use App\Domain\Achievement\Ports\AchievementGroupDALInterface;
use App\Domain\Core\Exceptions\EntityNotFoundException;

final readonly class GetAchievementGroupByIdUseCase
{
    public function __construct(
        private AchievementGroupDALInterface $achievementGroupDAL,
    ) {
    }

    public function execute(GetAchievementGroupByIdDTOInterface $dto): AchievementGroup
    {
        $achievementGroup = $this->achievementGroupDAL->getById($dto->getId());
        if (!$achievementGroup instanceof AchievementGroup) {
            throw new EntityNotFoundException();
        }

        return $achievementGroup;
    }
}

