<?php

namespace App\Domain\Achievement\Achievement;

use App\Domain\Achievement\Entity\Achievement;
use App\Domain\Achievement\Ports\AchievementDALInterface;
use Doctrine\ORM\EntityNotFoundException;

final readonly class GetAchievementByIdUseCase
{
    public function __construct(
        private AchievementDALInterface $achievementDAL,
    ) {
    }

    public function execute(GetAchievementByIdDTOInterface $dto): Achievement
    {
        $achievement = $this->achievementDAL->getById($dto->getId());
        if (!$achievement instanceof Achievement) {
            throw new EntityNotFoundException();
        }

        return $achievement;
    }
}

