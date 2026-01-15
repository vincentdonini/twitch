<?php

namespace App\Domain\Exercise\ExerciseCategory;

use App\Domain\Content\Entity\ContentExerciseCategory;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Exercise\Entity\ExerciseCategory;
use App\Domain\Exercise\Ports\ExerciseCategoryDALInterface;
use App\UI\Adapters\Http\Exercise\ExerciseCategory\UpsertContentExerciseCategoryHttp;
use Doctrine\ORM\EntityNotFoundException;

readonly class UpsertContentExerciseCategoryUseCase
{
    public function __construct(
        private DatabaseInterface            $database,
        private ExerciseCategoryDALInterface $exerciseCategoryDAL,
    ) {
    }

    public function execute(
        UpsertContentExerciseCategoryHttp $dto
    ): ContentExerciseCategory {
        $exerciseCategory = $this->exerciseCategoryDAL->getById($dto->getId());
        if (!$exerciseCategory instanceof ExerciseCategory) {
            throw new EntityNotFoundException();
        }

        if (!$this->validatePayload($dto)) {
            throw new InvalidPayloadException();
        }

        $contentExerciseCategory = $exerciseCategory->getContentByLocale($dto->getLocale());

        if (!$contentExerciseCategory) {
            $contentExerciseCategory = new ContentExerciseCategory(
                exerciseCategory: $exerciseCategory,
                locale          : $dto->getLocale(),
                title           : $dto->getTitle(),
                summary         : $dto->getSummary(),
                details         : $dto->getDetails()
            );

            $exerciseCategory->addContent($contentExerciseCategory);
        } else {
            $contentExerciseCategory
                ->setTitle($dto->getTitle())
                ->setSummary($dto->getSummary());

            if ($dto->getDetails() !== null) {
                $contentExerciseCategory->setDetails($dto->getDetails());
            }
        }

        $this->database->preSave($contentExerciseCategory);
        $this->database->save();

        return $contentExerciseCategory;
    }

    private function validatePayload(UpsertContentExerciseCategoryHttp $dto): bool
    {
        if (
            !$dto->getLocale() ||
            !$dto->getTitle() ||
            !$dto->getSummary()
        ) {
            return false;
        }
        return true;
    }
}

