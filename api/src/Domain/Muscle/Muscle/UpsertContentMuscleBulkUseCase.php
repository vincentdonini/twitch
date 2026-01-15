<?php

namespace App\Domain\Muscle\Muscle;

use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Muscle\Entity\Muscle;
use App\Domain\Muscle\Ports\MuscleDALInterface;
use App\UI\Adapters\Http\Muscle\Muscle\UpsertContentMuscleBulkHttp;
use App\UI\Adapters\Http\Muscle\Muscle\UpsertContentMuscleHttp;
use Doctrine\ORM\EntityNotFoundException;

readonly class UpsertContentMuscleBulkUseCase
{
    public function __construct(
        private DatabaseInterface          $database,
        private MuscleDALInterface         $muscleDAL,
        private UpsertContentMuscleUseCase $upsertContentMuscleUseCase,
    ) {
    }

    public function execute(UpsertContentMuscleBulkHttp $dto): void
    {
        $muscle = $this->muscleDAL->getById($dto->getId());
        if (!$muscle instanceof Muscle) {
            throw new EntityNotFoundException();
        }

        $this->validatePayload($dto->getContents());

        $this->database->beginTransaction();

        try {
            foreach ($dto->getContents() as $locale => $contentData) {
                $this->upsertContentMuscleUseCase->execute(
                    new UpsertContentMuscleHttp(
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
