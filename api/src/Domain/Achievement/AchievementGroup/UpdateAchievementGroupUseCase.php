<?php

namespace App\Domain\Achievement\AchievementGroup;

use App\Domain\Achievement\Entity\AchievementGroup;
use App\Domain\Achievement\Ports\AchievementCategoryDALInterface;
use App\Domain\Core\Exceptions\AlreadyExistException;
use App\Domain\Core\Exceptions\EntityNotFoundException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Achievement\Ports\AchievementGroupDALInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UpdateAchievementGroupUseCase
{
    public function __construct(
        private DatabaseInterface               $database,
        private AchievementGroupDALInterface    $achievementGroupDAL,
        private AchievementCategoryDALInterface $achievementCategoryDAL,
    ) {
    }

    public function execute(
        UpdateAchievementGroupDTOInterface $dto
    ): AchievementGroup {
        $achievementGroup = $this->achievementGroupDAL->getById($dto->getId());
        if (!$achievementGroup instanceof AchievementGroup) {
            throw new EntityNotFoundException();
        }

        if ($dto->getAchievementCategoryId() !== null) {
            $achievementCategory = $this->achievementCategoryDAL->getById($dto->getAchievementCategoryId());
            $achievementGroup->setAchievementCategory($achievementCategory);
        }

        if (!empty($dto->getCode())) {
            if (!$this->validateDuplicateField($dto->getCode(), $achievementGroup->getId())) {
                throw new AlreadyExistException();
            }
            $achievementGroup->setCode($dto->getCode());
        }

        if (!empty($dto->getPosition())) {
            $achievementGroup->setPosition($dto->getPosition());
        }

        $this->database->preSave($achievementGroup);
        $this->database->save();

        return $achievementGroup;
    }

    private function validateDuplicateField(string $value, Uuid $currentAchievementGroupId): bool
    {
        $existingAchievementGroup = $this->achievementGroupDAL->findOneBy(['code' => $value]);

        if (!$existingAchievementGroup) {
            return true;
        }

        return $existingAchievementGroup->getId()->equals($currentAchievementGroupId);
    }
}
