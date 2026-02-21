<?php

namespace App\Domain\Achievement\Achievement;

use App\Domain\Achievement\Ports\AchievementGroupDALInterface;
use App\Domain\Core\Exceptions\AlreadyExistException;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Achievement\Entity\Achievement;
use App\Domain\Achievement\Ports\AchievementDALInterface;

final readonly class CreateAchievementUseCase
{
    public function __construct(
        private DatabaseInterface            $database,
        private AchievementDALInterface      $achievementDAL,
        private AchievementGroupDALInterface $achievementGroupDAL,
    ) {
    }

    public function execute(CreateAchievementDTOInterface $dto): Achievement
    {

        if (!$this->validatePayload($dto)) {
            throw new InvalidPayloadException();
        }

        if (!$this->validateDuplicate($dto->getCode())) {
            throw new AlreadyExistException();
        }

        $achievementGroup = $this->achievementGroupDAL->getById($dto->getAchievementGroupId());
        if (!$achievementGroup) {
            throw new InvalidPayloadException();
        }

        $achievement = new Achievement(
            code            : $dto->getCode(),
            position        : $dto->getPosition(),
            source          : $dto->getSource(),
            achievementGroup: $achievementGroup,
        );

        $this->database->preSave($achievement);
        $this->database->save();

        return $achievement;
    }

    private function validatePayload(CreateAchievementDTOInterface $dto): bool
    {
        if (
            !$dto->getCode() ||
            !$dto->getPosition() ||
            !$dto->getSource() ||
            !$dto->getAchievementGroupId()
        ) {
            return false;
        }
        return true;
    }

    private function validateDuplicate(string $code): bool
    {
        return !$this->achievementDAL->findOneBy(['code' => $code]);
    }
}
