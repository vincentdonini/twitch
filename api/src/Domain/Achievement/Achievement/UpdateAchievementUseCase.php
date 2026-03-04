<?php

namespace App\Domain\Achievement\Achievement;

use App\Domain\Core\Exceptions\AlreadyExistException;
use App\Domain\Core\Exceptions\EntityNotFoundException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Achievement\Entity\Achievement;
use App\Domain\Achievement\Ports\AchievementDALInterface;
use App\Domain\Achievement\Ports\AchievementGroupDALInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UpdateAchievementUseCase
{
    public function __construct(
        private DatabaseInterface            $database,
        private AchievementDALInterface      $achievementDAL,
        private AchievementGroupDALInterface $achievementGroupDAL,
    ) {
    }

    public function execute(
        UpdateAchievementDTOInterface $dto
    ): Achievement {
        $achievement = $this->achievementDAL->getById($dto->getId());
        if (!$achievement instanceof Achievement) {
            throw new EntityNotFoundException();
        }

        if ($dto->getAchievementGroupId() !== null) {
            $achievementGroup = $this->achievementGroupDAL->getById($dto->getAchievementGroupId());
            $achievement->setAchievementGroup($achievementGroup);
        }

        if (!empty($dto->getCode())) {
            if (!$this->validateDuplicateField($dto->getCode(), $achievement->getId())) {
                throw new AlreadyExistException();
            }
            $achievement->setCode($dto->getCode());
        }

        if (!empty($dto->getPosition())) {
            $achievement->setPosition($dto->getPosition());
        }

        $this->database->preSave($achievement);
        $this->database->save();

        return $achievement;
    }

    private function validateDuplicateField(string $value, Uuid $currentAchievementId): bool
    {
        $existingAchievement = $this->achievementDAL->findOneBy(['code' => $value]);

        if (!$existingAchievement) {
            return true;
        }

        return $existingAchievement->getId()->equals($currentAchievementId);
    }
}
