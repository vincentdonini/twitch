<?php

namespace App\Domain\Muscle\MuscleArea;

use App\Domain\Content\Entity\ContentMuscleArea;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Muscle\Entity\MuscleArea;
use App\Domain\Muscle\Ports\MuscleAreaDALInterface;
use App\UI\Adapters\Http\Muscle\MuscleArea\UpsertContentMuscleAreaHttp;
use Doctrine\ORM\EntityNotFoundException;

readonly class UpsertContentMuscleAreaUseCase
{
    public function __construct(
        private DatabaseInterface      $database,
        private MuscleAreaDALInterface $muscleAreaDAL,
    ) {
    }

    public function execute(
        UpsertContentMuscleAreaHttp $dto
    ): ContentMuscleArea {
        $muscleArea = $this->muscleAreaDAL->getById($dto->getId());
        if (!$muscleArea instanceof MuscleArea) {
            throw new EntityNotFoundException();
        }

        if (!$this->validatePayload($dto)) {
            throw new InvalidPayloadException();
        }

        $contentMuscleArea = $muscleArea->getContentByLocale($dto->getLocale());

        if (!$contentMuscleArea) {
            $contentMuscleArea = new ContentMuscleArea(
                muscleArea: $muscleArea,
                locale    : $dto->getLocale(),
                title     : $dto->getTitle(),
                summary   : $dto->getSummary(),
                details   : $dto->getDetails()
            );

            $muscleArea->addContent($contentMuscleArea);
        } else {
            $contentMuscleArea
                ->setTitle($dto->getTitle())
                ->setSummary($dto->getSummary());

            if ($dto->getDetails() !== null) {
                $contentMuscleArea->setDetails($dto->getDetails());
            }
        }

        $this->database->preSave($contentMuscleArea);
        $this->database->save();

        return $contentMuscleArea;
    }

    private function validatePayload(UpsertContentMuscleAreaHttp $dto): bool
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

