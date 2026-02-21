<?php

namespace App\Domain\Achievement\AchievementCategory;

use App\Domain\Achievement\Entity\AchievementCategory;
use App\Domain\Achievement\Ports\AchievementCategoryDALInterface;
use App\Domain\Core\Exceptions\AlreadyExistException;
use App\Domain\Core\Exceptions\EntityNotFoundException;
use App\Domain\Core\Ports\DatabaseInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UpdateAchievementCategoryUseCase
{
    public function __construct(
        private DatabaseInterface               $database,
        private AchievementCategoryDALInterface $achievementCategoryDAL,
    ) {
    }

    public function execute(
        UpdateAchievementCategoryDTOInterface $dto
    ): AchievementCategory {
        $achievementCategory = $this->achievementCategoryDAL->getById($dto->getId());
        if (!$achievementCategory instanceof AchievementCategory) {
            throw new EntityNotFoundException();
        }

        if (!empty($dto->getCode())) {
            if (!$this->validateDuplicateField($dto->getCode(), $achievementCategory->getId())) {
                throw new AlreadyExistException();
            }
            $achievementCategory->setCode($dto->getCode());
        }

        if (!empty($dto->getPosition())) {
            $achievementCategory->setPosition($dto->getPosition());
        }

        $this->database->preSave($achievementCategory);
        $this->database->save();

        return $achievementCategory;
    }

    private function validateDuplicateField(string $value, Uuid $currentAchievementCategoryId): bool
    {
        $existingAchievementCategory = $this->achievementCategoryDAL->findOneBy(['code' => $value]);

        if (!$existingAchievementCategory) {
            return true;
        }

        return $existingAchievementCategory->getId() === $currentAchievementCategoryId;
    }
}
