<?php

namespace App\Domain\Muscle\MuscleSegment;

use App\Domain\Content\Entity\ContentMuscleSegment;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Muscle\Entity\MuscleSegment;
use App\Domain\Muscle\Ports\MuscleSegmentDALInterface;
use App\UI\Adapters\Http\Muscle\MuscleSegment\UpsertContentMuscleSegmentHttp;
use Doctrine\ORM\EntityNotFoundException;

final readonly class UpsertContentMuscleSegmentUseCase
{
    public function __construct(
        private DatabaseInterface         $database,
        private MuscleSegmentDALInterface $muscleSegmentDAL,
    ) {
    }

    public function execute(UpsertContentMuscleSegmentHttp $dto): ContentMuscleSegment
    {
        $segment = $this->muscleSegmentDAL->getById($dto->getId());
        if (!$segment instanceof MuscleSegment) {
            throw new EntityNotFoundException();
        }

        if (!$this->validatePayload($dto)) {
            throw new InvalidPayloadException();
        }

        $content = $segment->getContentByLocale($dto->getLocale());

        if (!$content) {
            $content = new ContentMuscleSegment(
                muscleSegment: $segment,
                locale       : $dto->getLocale(),
                title        : $dto->getTitle(),
                summary      : $dto->getSummary(),
                details      : $dto->getDetails(),
            );
            $segment->addContent($content);
        } else {
            $content->setTitle($dto->getTitle())
                    ->setSummary($dto->getSummary());

            if ($dto->getDetails() !== null) {
                $content->setDetails($dto->getDetails());
            }
        }

        $this->database->preSave($content);
        $this->database->save();

        return $content;
    }

    private function validatePayload(UpsertContentMuscleSegmentHttp $dto): bool
    {
        return $dto->getLocale() && $dto->getTitle() && $dto->getSummary();
    }
}
