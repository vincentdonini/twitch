<?php

namespace App\Domain\Achievement\AchievementCategory;

use App\Domain\Achievement\Entity\AchievementCategory;
use App\Domain\Achievement\Ports\AchievementCategoryDALInterface;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\UI\Adapters\Http\Achievement\AchievementCategory\UpsertContentAchievementCategoryBulkHttp;
use App\UI\Adapters\Http\Achievement\AchievementCategory\UpsertContentAchievementCategoryHttp;
use Doctrine\ORM\EntityNotFoundException;

final readonly class UpsertContentAchievementCategoryBulkUseCase
{
    public function __construct(
        private DatabaseInterface                       $database,
        private AchievementCategoryDALInterface         $achievementCategoryDAL,
        private UpsertContentAchievementCategoryUseCase $upsertContentAchievementCategoryUseCase,
    ) {
    }

    public function execute(
        UpsertContentAchievementCategoryBulkHttp $dto
    ): void {
        $achievementCategory = $this->achievementCategoryDAL->getById($dto->getId());
        if (!$achievementCategory instanceof AchievementCategory) {
            throw new EntityNotFoundException();
        }

        $this->validatePayload($dto->getContents());

        $this->database->beginTransaction();

        try {
            foreach ($dto->getContents() as $locale => $contentData) {
                $this->upsertContentAchievementCategoryUseCase->execute(
                    new UpsertContentAchievementCategoryHttp(
                        id     : $dto->getId(),
                        locale : $locale,
                        payload: $contentData
                    )
                );
            }

            $this->database->commit();
        } catch (\Throwable $e) {
            $this->database->rollback();
            throw $e;
        }
    }

    private function validatePayload(array $contents): void
    {
        if (empty($contents)) {
            throw new InvalidPayloadException();
        }

        foreach ($contents as $locale => $content) {
            if (!preg_match('/^[a-z]{2}$/', $locale)) {
                throw new InvalidPayloadException();
            }

            if (!is_array($content)) {
                throw new InvalidPayloadException();
            }
        }
    }
}
