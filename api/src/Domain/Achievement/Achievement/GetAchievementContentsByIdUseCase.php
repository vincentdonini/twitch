<?php

namespace App\Domain\Achievement\Achievement;

use App\Domain\Achievement\Entity\Achievement;
use App\Domain\Achievement\Ports\AchievementDALInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityNotFoundException;

final readonly class GetAchievementContentsByIdUseCase
{
    public function __construct(
        private AchievementDALInterface $achievementDAL,
    ) {

    }

    public function execute(GetAchievementByIdDTOInterface $dto): Collection
    {
        $achievement = $this->achievementDAL->getById($dto->getId());
        if (!$achievement instanceof Achievement) {
            throw new EntityNotFoundException();
        }

        return $achievement->getContents();
    }
}

