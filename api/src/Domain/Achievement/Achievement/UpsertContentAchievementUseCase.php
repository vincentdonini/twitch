<?php

namespace App\Domain\Achievement\Achievement;

use App\Domain\Achievement\Entity\Achievement;
use App\Domain\Achievement\Ports\AchievementDALInterface;
use App\Domain\Content\Entity\ContentAchievement;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\UI\Adapters\Http\Achievement\Achievement\UpsertContentAchievementHttp;
use Doctrine\ORM\EntityNotFoundException;

final readonly class UpsertContentAchievementUseCase
{
    public function __construct(
        private DatabaseInterface       $database,
        private AchievementDALInterface $achievementDAL,
    ) {
    }

    public function execute(
        UpsertContentAchievementHttp $dto
    ): ContentAchievement {
        $achievement = $this->achievementDAL->getById($dto->getId());
        if (!$achievement instanceof Achievement) {
            throw new EntityNotFoundException();
        }

        if (!$this->validatePayload($dto)) {
            throw new InvalidPayloadException();
        }

        $contentAchievement = $achievement->getContentByLocale($dto->getLocale());

        if (!$contentAchievement) {
            $contentAchievement = new ContentAchievement(
                achievement: $achievement,
                locale     : $dto->getLocale(),
                title      : $dto->getTitle(),
                description: $dto->getDescription(),
            );

            $achievement->addContent($contentAchievement);
        } else {
            $contentAchievement
                ->setTitle($dto->getTitle())
                ->setDescription($dto->getDescription());
        }

        $this->database->preSave($contentAchievement);
        $this->database->save();

        return $contentAchievement;
    }

    private function validatePayload(UpsertContentAchievementHttp $dto): bool
    {
        if (
            !$dto->getLocale() ||
            !$dto->getTitle() ||
            !$dto->getDescription()
        ) {
            return false;
        }
        return true;
    }
}

