<?php

namespace App\Domain\Muscle\MuscleGroup;

use App\Domain\Content\Entity\ContentMuscleGroup;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Muscle\Entity\MuscleGroup;
use App\Domain\Muscle\Ports\MuscleGroupDALInterface;
use App\UI\Adapters\Http\Muscle\MuscleGroup\UpsertContentMuscleGroupHttp;
use Doctrine\ORM\EntityNotFoundException;

readonly class UpsertContentMuscleGroupUseCase
{
    public function __construct(
        private DatabaseInterface       $database,
        private MuscleGroupDALInterface $muscleGroupDAL,
    ) {
    }

    public function execute(
        UpsertContentMuscleGroupHttp $dto
    ): ContentMuscleGroup {
        $muscleGroup = $this->muscleGroupDAL->getById($dto->getId());
        if (!$muscleGroup instanceof MuscleGroup) {
            throw new EntityNotFoundException();
        }

        if (!$this->validatePayload($dto)) {
            throw new InvalidPayloadException();
        }

        $contentMuscleGroup = $muscleGroup->getContentByLocale($dto->getLocale());

        if (!$contentMuscleGroup) {
            $contentMuscleGroup = new ContentMuscleGroup(
                muscleGroup: $muscleGroup,
                locale     : $dto->getLocale(),
                title      : $dto->getTitle(),
                summary    : $dto->getSummary(),
                details    : $dto->getDetails()
            );

            $muscleGroup->addContent($contentMuscleGroup);
        } else {
            $contentMuscleGroup
                ->setTitle($dto->getTitle())
                ->setSummary($dto->getSummary());

            if ($dto->getDetails() !== null) {
                $contentMuscleGroup->setDetails($dto->getDetails());
            }
        }

        $this->database->preSave($contentMuscleGroup);
        $this->database->save();

        return $contentMuscleGroup;
    }

    private function validatePayload(UpsertContentMuscleGroupHttp $dto): bool
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

