<?php

namespace App\Domain\Muscle\Muscle;

use App\Domain\Content\Entity\ContentMuscle;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Muscle\Entity\Muscle;
use App\Domain\Muscle\Ports\MuscleDALInterface;
use App\UI\Adapters\Http\Muscle\Muscle\UpsertContentMuscleHttp;
use Doctrine\ORM\EntityNotFoundException;

final readonly class UpsertContentMuscleUseCase
{
    public function __construct(
        private DatabaseInterface  $database,
        private MuscleDALInterface $muscleDAL,
    ) {
    }

    public function execute(
        UpsertContentMuscleHttp $dto
    ): ContentMuscle {
        $muscle = $this->muscleDAL->getById($dto->getId());
        if (!$muscle instanceof Muscle) {
            throw new EntityNotFoundException();
        }

        if (!$this->validatePayload($dto)) {
            throw new InvalidPayloadException();
        }

        $contentMuscle = $muscle->getContentByLocale($dto->getLocale());

        if (!$contentMuscle) {
            $contentMuscle = new ContentMuscle(
                muscle : $muscle,
                locale : $dto->getLocale(),
                title  : $dto->getTitle(),
                summary: $dto->getSummary(),
                details: $dto->getDetails()
            );

            $muscle->addContent($contentMuscle);
        } else {
            $contentMuscle
                ->setTitle($dto->getTitle())
                ->setSummary($dto->getSummary());

            if ($dto->getDetails() !== null) {
                $contentMuscle->setDetails($dto->getDetails());
            }
        }

        $this->database->preSave($contentMuscle);
        $this->database->save();

        return $contentMuscle;
    }

    private function validatePayload(UpsertContentMuscleHttp $dto): bool
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
