<?php

namespace App\Domain\Exercise\Exercise;

use App\Domain\Content\Entity\ContentExercise;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Exercise\Entity\Exercise;
use App\Domain\Exercise\Ports\ExerciseDALInterface;
use App\UI\Adapters\Http\Exercise\Exercise\UpsertContentExerciseHttp;
use Doctrine\ORM\EntityNotFoundException;

readonly class UpsertContentExerciseUseCase
{
    public function __construct(
        private DatabaseInterface    $database,
        private ExerciseDALInterface $exerciseDAL,
    ) {
    }

    public function execute(
        UpsertContentExerciseHttp $dto
    ): ContentExercise {
        $exercise = $this->exerciseDAL->getById($dto->getId());
        if (!$exercise instanceof Exercise) {
            throw new EntityNotFoundException();
        }

        if (!$this->validatePayload($dto)) {
            throw new InvalidPayloadException();
        }

        $contentExercise = $exercise->getContentByLocale($dto->getLocale());

        if (!$contentExercise) {
            $contentExercise = new ContentExercise(
                exercise: $exercise,
                locale  : $dto->getLocale(),
                title   : $dto->getTitle(),
                summary : $dto->getSummary(),
                details : $dto->getDetails()
            );

            $exercise->addContent($contentExercise);
        } else {
            $contentExercise
                ->setTitle($dto->getTitle())
                ->setSummary($dto->getSummary());

            if ($dto->getDetails() !== null) {
                $contentExercise->setDetails($dto->getDetails());
            }
        }

        $this->database->preSave($contentExercise);
        $this->database->save();

        return $contentExercise;
    }

    private function validatePayload(UpsertContentExerciseHttp $dto): bool
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

