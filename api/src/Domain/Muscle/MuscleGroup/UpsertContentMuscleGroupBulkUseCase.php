<?php

namespace App\Domain\Muscle\MuscleGroup;

use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Muscle\Entity\MuscleGroup;
use App\Domain\Muscle\Ports\MuscleGroupDALInterface;
use App\UI\Adapters\Http\Muscle\MuscleGroup\UpsertContentMuscleGroupBulkHttp;
use App\UI\Adapters\Http\Muscle\MuscleGroup\UpsertContentMuscleGroupHttp;
use Doctrine\ORM\EntityNotFoundException;

readonly class UpsertContentMuscleGroupBulkUseCase
{
    public function __construct(
        private DatabaseInterface               $database,
        private MuscleGroupDALInterface         $muscleGroupDAL,
        private UpsertContentMuscleGroupUseCase $upsertContentMuscleGroupUseCase,
    ) {
    }

    public function execute(UpsertContentMuscleGroupBulkHttp $dto): void
    {
        $muscleGroup = $this->muscleGroupDAL->getById($dto->getId());
        if (!$muscleGroup instanceof MuscleGroup) {
            throw new EntityNotFoundException();
        }

        $this->validatePayload($dto->getContents());

        $this->database->beginTransaction();

        try {
            foreach ($dto->getContents() as $locale => $contentData) {
                $this->upsertContentMuscleGroupUseCase->execute(
                    new UpsertContentMuscleGroupHttp(
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
