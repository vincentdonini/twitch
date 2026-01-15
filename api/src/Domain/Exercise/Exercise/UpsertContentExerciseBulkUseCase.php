<?php

namespace App\Domain\Exercise\Exercise;

use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Exercise\Entity\Exercise;
use App\Domain\Exercise\Ports\ExerciseDALInterface;
use App\UI\Adapters\Http\Exercise\Exercise\UpsertContentExerciseBulkHttp;
use App\UI\Adapters\Http\Exercise\Exercise\UpsertContentExerciseHttp;
use Doctrine\ORM\EntityNotFoundException;

readonly class UpsertContentExerciseBulkUseCase
{
    public function __construct(
        private DatabaseInterface            $database,
        private ExerciseDALInterface         $exerciseDAL,
        private UpsertContentExerciseUseCase $upsertContentExerciseUseCase,
    ) {
    }

    public function execute(UpsertContentExerciseBulkHttp $dto): void
    {
        $exercise = $this->exerciseDAL->getById($dto->getId());
        if (!$exercise instanceof Exercise) {
            throw new EntityNotFoundException();
        }

        $this->validatePayload($dto->getContents());

        $this->database->beginTransaction();

        try {
            foreach ($dto->getContents() as $locale => $contentData) {
                $this->upsertContentExerciseUseCase->execute(
                    new UpsertContentExerciseHttp(
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
