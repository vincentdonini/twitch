<?php

namespace App\Domain\Achievement\AchievementGroup;

use App\Domain\Achievement\Entity\AchievementGroup;
use App\Domain\Achievement\Ports\AchievementGroupDALInterface;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\UI\Adapters\Http\Achievement\AchievementGroup\UpsertContentAchievementGroupBulkHttp;
use App\UI\Adapters\Http\Achievement\AchievementGroup\UpsertContentAchievementGroupHttp;
use Doctrine\ORM\EntityNotFoundException;

final readonly class UpsertContentAchievementGroupBulkUseCase
{
    public function __construct(
        private DatabaseInterface                    $database,
        private AchievementGroupDALInterface         $achievementGroupDAL,
        private UpsertContentAchievementGroupUseCase $upsertContentAchievementGroupUseCase,
    ) {
    }

    public function execute(
        UpsertContentAchievementGroupBulkHttp $dto
    ): void {
        $achievementGroup = $this->achievementGroupDAL->getById($dto->getId());
        if (!$achievementGroup instanceof AchievementGroup) {
            throw new EntityNotFoundException();
        }

        $this->validatePayload($dto->getContents());

        $this->database->beginTransaction();

        try {
            foreach ($dto->getContents() as $locale => $contentData) {
                $this->upsertContentAchievementGroupUseCase->execute(
                    new UpsertContentAchievementGroupHttp(
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
