<?php

namespace App\Domain\Muscle\MuscleSegment;

use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Muscle\Entity\MuscleSegment;
use App\Domain\Muscle\Ports\MuscleSegmentDALInterface;
use App\UI\Adapters\Http\Muscle\MuscleSegment\UpsertContentMuscleSegmentBulkHttp;
use App\UI\Adapters\Http\Muscle\MuscleSegment\UpsertContentMuscleSegmentHttp;
use Doctrine\ORM\EntityNotFoundException;

final readonly class UpsertContentMuscleSegmentBulkUseCase
{
    public function __construct(
        private DatabaseInterface                    $database,
        private MuscleSegmentDALInterface            $muscleSegmentDAL,
        private UpsertContentMuscleSegmentUseCase    $upsertUseCase,
    ) {
    }

    public function execute(UpsertContentMuscleSegmentBulkHttp $dto): void
    {
        $segment = $this->muscleSegmentDAL->getById($dto->getId());
        if (!$segment instanceof MuscleSegment) {
            throw new EntityNotFoundException();
        }

        $this->validatePayload($dto->getContents());

        $this->database->beginTransaction();

        try {
            foreach ($dto->getContents() as $locale => $contentData) {
                $this->upsertUseCase->execute(
                    new UpsertContentMuscleSegmentHttp(
                        id     : $dto->getId(),
                        locale : $locale,
                        payload: $contentData,
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
