<?php

namespace App\Domain\Achievement\AchievementGroup;

use App\Domain\Achievement\Entity\AchievementGroup;
use App\Domain\Achievement\Ports\AchievementGroupDALInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityNotFoundException;

final readonly class GetAchievementGroupContentsByIdUseCase
{
    public function __construct(
        private AchievementGroupDALInterface $achievementGroupDAL,
    ) {
    }

    public function execute(GetAchievementGroupByIdDTOInterface $dto): Collection
    {
        $achievementGroup = $this->achievementGroupDAL->getById($dto->getId());
        if (!$achievementGroup instanceof AchievementGroup) {
            throw new EntityNotFoundException();
        }

        return $achievementGroup->getContents();
    }
}

