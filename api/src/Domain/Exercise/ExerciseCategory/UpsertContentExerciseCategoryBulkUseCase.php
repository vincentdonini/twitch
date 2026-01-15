<?php

namespace App\Domain\Exercise\ExerciseCategory;

use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Exercise\Entity\ExerciseCategory;
use App\Domain\Exercise\Ports\ExerciseCategoryDALInterface;
use App\UI\Adapters\Http\Exercise\ExerciseCategory\UpsertContentExerciseCategoryBulkHttp;
use App\UI\Adapters\Http\Exercise\ExerciseCategory\UpsertContentExerciseCategoryHttp;
use Doctrine\ORM\EntityNotFoundException;

readonly class UpsertContentExerciseCategoryBulkUseCase
{
    public function __construct(
        private DatabaseInterface                    $database,
        private ExerciseCategoryDALInterface         $exerciseCategoryDAL,
        private UpsertContentExerciseCategoryUseCase $upsertContentExerciseCategoryUseCase,
    ) {
    }

    public function execute(UpsertContentExerciseCategoryBulkHttp $dto): void
    {
        $exerciseCategory = $this->exerciseCategoryDAL->getById($dto->getId());
        if (!$exerciseCategory instanceof ExerciseCategory) {
            throw new EntityNotFoundException();
        }

        $this->validatePayload($dto->getContents());

        $this->database->beginTransaction();

        try {
            foreach ($dto->getContents() as $locale => $contentData) {
                $this->upsertContentExerciseCategoryUseCase->execute(
                    new UpsertContentExerciseCategoryHttp(
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
