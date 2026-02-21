<?php

namespace App\Domain\Achievement\AchievementCategory;

use App\Domain\Achievement\Entity\AchievementCategory;
use App\Domain\Achievement\Ports\AchievementCategoryDALInterface;
use App\Domain\Core\Exceptions\AlreadyExistException;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;

final readonly class CreateAchievementCategoryUseCase
{
    public function __construct(
        private DatabaseInterface               $database,
        private AchievementCategoryDALInterface $achievementCategoryDAL,
    ) {
    }

    public function execute(CreateAchievementCategoryDTOInterface $dto): AchievementCategory
    {

        if (!$this->validatePayload($dto)) {
            throw new InvalidPayloadException();
        }

        if (!$this->validateDuplicate($dto->getCode())) {
            throw new AlreadyExistException();
        }

        $achievementCategory = new AchievementCategory(
            code    : $dto->getCode(),
            position: $dto->getPosition(),
        );

        $this->database->preSave($achievementCategory);
        $this->database->save();

        return $achievementCategory;
    }

    private function validatePayload(CreateAchievementCategoryDTOInterface $dto): bool
    {
        if (
            !$dto->getCode() ||
            !$dto->getPosition()
        ) {
            return false;
        }
        return true;
    }

    private function validateDuplicate(string $code): bool
    {
        return !$this->achievementCategoryDAL->findOneBy(['code' => $code]);
    }
}
