<?php

namespace App\Domain\Achievement\AchievementGroup;

use App\Domain\Achievement\Entity\AchievementGroup;
use App\Domain\Achievement\Ports\AchievementCategoryDALInterface;
use App\Domain\Achievement\Ports\AchievementGroupDALInterface;
use App\Domain\Core\Exceptions\AlreadyExistException;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;

final readonly class CreateAchievementGroupUseCase
{
    public function __construct(
        private DatabaseInterface               $database,
        private AchievementGroupDALInterface    $achievementGroupDAL,
        private AchievementCategoryDALInterface $achievementCategoryDAL,
    ) {
    }

    public function execute(CreateAchievementGroupDTOInterface $dto): AchievementGroup
    {

        if (!$this->validatePayload($dto)) {
            throw new InvalidPayloadException();
        }

        if (!$this->validateDuplicate($dto->getCode())) {
            throw new AlreadyExistException();
        }

        $achievementCategory = $this->achievementCategoryDAL->getById($dto->getAchievementCategoryId());
        if (!$achievementCategory) {
            throw new InvalidPayloadException();
        }

        $achievementGroup = new AchievementGroup(
            code               : $dto->getCode(),
            position           : $dto->getPosition(),
            achievementCategory: $achievementCategory,
        );

        $this->database->preSave($achievementGroup);
        $this->database->save();

        return $achievementGroup;
    }

    private function validatePayload(CreateAchievementGroupDTOInterface $dto): bool
    {
        if (
            !$dto->getCode() ||
            !$dto->getPosition() ||
            !$dto->getAchievementCategoryId()
        ) {
            return false;
        }
        return true;
    }

    private function validateDuplicate(string $code): bool
    {
        return !$this->achievementGroupDAL->findOneBy(['code' => $code]);
    }
}
