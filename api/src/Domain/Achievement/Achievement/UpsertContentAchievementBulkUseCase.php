<?php

namespace App\Domain\Achievement\Achievement;

use App\Domain\Achievement\Entity\Achievement;
use App\Domain\Achievement\Ports\AchievementDALInterface;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\UI\Adapters\Http\Achievement\Achievement\UpsertContentAchievementBulkHttp;
use App\UI\Adapters\Http\Achievement\Achievement\UpsertContentAchievementHttp;
use Doctrine\ORM\EntityNotFoundException;

final readonly class UpsertContentAchievementBulkUseCase
{
    public function __construct(
        private DatabaseInterface               $database,
        private AchievementDALInterface         $achievementDAL,
        private UpsertContentAchievementUseCase $upsertContentAchievementUseCase,
    ) {
    }

    public function execute(
        UpsertContentAchievementBulkHttp $dto
    ): void {
        $achievement = $this->achievementDAL->getById($dto->getId());
        if (!$achievement instanceof Achievement) {
            throw new EntityNotFoundException();
        }

        $this->validatePayload($dto->getContents());

        $this->database->beginTransaction();

        try {
            foreach ($dto->getContents() as $locale => $contentData) {
                $this->upsertContentAchievementUseCase->execute(
                    new UpsertContentAchievementHttp(
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
