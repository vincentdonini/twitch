<?php

namespace App\Domain\Muscle\MuscleArea;

use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Muscle\Entity\MuscleArea;
use App\Domain\Muscle\Ports\MuscleAreaDALInterface;
use App\UI\Adapters\Http\Muscle\MuscleArea\UpsertContentMuscleAreaBulkHttp;
use App\UI\Adapters\Http\Muscle\MuscleArea\UpsertContentMuscleAreaHttp;
use Doctrine\ORM\EntityNotFoundException;

readonly class UpsertContentMuscleAreaBulkUseCase
{
    public function __construct(
        private DatabaseInterface              $database,
        private MuscleAreaDALInterface         $muscleAreaDAL,
        private UpsertContentMuscleAreaUseCase $upsertContentMuscleAreaUseCase,
    ) {
    }

    public function execute(UpsertContentMuscleAreaBulkHttp $dto): void
    {
        $muscleArea = $this->muscleAreaDAL->getById($dto->getId());
        if (!$muscleArea instanceof MuscleArea) {
            throw new EntityNotFoundException();
        }

        $this->validatePayload($dto->getContents());

        $this->database->beginTransaction();

        try {
            foreach ($dto->getContents() as $locale => $contentData) {
                $this->upsertContentMuscleAreaUseCase->execute(
                    new UpsertContentMuscleAreaHttp(
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
