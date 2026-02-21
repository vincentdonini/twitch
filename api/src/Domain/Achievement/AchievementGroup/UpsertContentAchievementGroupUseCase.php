<?php

namespace App\Domain\Achievement\AchievementGroup;

use App\Domain\Achievement\Entity\AchievementGroup;
use App\Domain\Achievement\Ports\AchievementGroupDALInterface;
use App\Domain\Content\Entity\ContentAchievementGroup;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\UI\Adapters\Http\Achievement\AchievementGroup\UpsertContentAchievementGroupHttp;
use Doctrine\ORM\EntityNotFoundException;

final readonly class UpsertContentAchievementGroupUseCase
{
    public function __construct(
        private DatabaseInterface            $database,
        private AchievementGroupDALInterface $achievementGroupDAL,
    ) {
    }

    public function execute(
        UpsertContentAchievementGroupHttp $dto
    ): ContentAchievementGroup {
        $achievementGroup = $this->achievementGroupDAL->getById($dto->getId());
        if (!$achievementGroup instanceof AchievementGroup) {
            throw new EntityNotFoundException();
        }

        if (!$this->validatePayload($dto)) {
            throw new InvalidPayloadException();
        }

        $contentAchievementGroup = $achievementGroup->getContentByLocale($dto->getLocale());

        if (!$contentAchievementGroup) {
            $contentAchievementGroup = new ContentAchievementGroup(
                achievementGroup: $achievementGroup,
                locale          : $dto->getLocale(),
                title           : $dto->getTitle(),
                description     : $dto->getDescription(),
            );

            $achievementGroup->addContent($contentAchievementGroup);
        } else {
            $contentAchievementGroup
                ->setTitle($dto->getTitle())
                ->setDescription($dto->getDescription());
        }

        $this->database->preSave($contentAchievementGroup);
        $this->database->save();

        return $contentAchievementGroup;
    }

    private function validatePayload(UpsertContentAchievementGroupHttp $dto): bool
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

