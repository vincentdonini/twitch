<?php

namespace App\Domain\Achievement\AchievementCategory;

use App\Domain\Achievement\Entity\AchievementCategory;
use App\Domain\Achievement\Ports\AchievementCategoryDALInterface;
use App\Domain\Content\Entity\ContentAchievementCategory;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\UI\Adapters\Http\Achievement\AchievementCategory\UpsertContentAchievementCategoryHttp;
use Doctrine\ORM\EntityNotFoundException;

final readonly class UpsertContentAchievementCategoryUseCase
{
    public function __construct(
        private DatabaseInterface               $database,
        private AchievementCategoryDALInterface $achievementCategoryDAL,
    ) {
    }

    public function execute(
        UpsertContentAchievementCategoryHttp $dto
    ): ContentAchievementCategory {
        $achievementCategory = $this->achievementCategoryDAL->getById($dto->getId());
        if (!$achievementCategory instanceof AchievementCategory) {
            throw new EntityNotFoundException();
        }

        if (!$this->validatePayload($dto)) {
            throw new InvalidPayloadException();
        }

        $contentAchievementCategory = $achievementCategory->getContentByLocale($dto->getLocale());

        if (!$contentAchievementCategory) {
            $contentAchievementCategory = new ContentAchievementCategory(
                achievementCategory: $achievementCategory,
                locale             : $dto->getLocale(),
                title              : $dto->getTitle(),
                description        : $dto->getDescription(),
            );

            $achievementCategory->addContent($contentAchievementCategory);
        } else {
            $contentAchievementCategory
                ->setTitle($dto->getTitle())
                ->setDescription($dto->getDescription());
        }

        $this->database->preSave($contentAchievementCategory);
        $this->database->save();

        return $contentAchievementCategory;
    }

    private function validatePayload(UpsertContentAchievementCategoryHttp $dto): bool
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

