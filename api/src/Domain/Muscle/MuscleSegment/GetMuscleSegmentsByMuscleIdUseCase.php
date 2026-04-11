<?php

namespace App\Domain\Muscle\MuscleSegment;

use App\Domain\Muscle\Entity\MuscleSegment;
use App\Domain\Muscle\Ports\MuscleDALInterface;
use App\Domain\Muscle\Ports\MuscleSegmentDALInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Uid\Uuid;

final readonly class GetMuscleSegmentsByMuscleIdUseCase
{
    public function __construct(
        private MuscleDALInterface        $muscleDAL,
        private MuscleSegmentDALInterface $muscleSegmentDAL,
    ) {
    }

    /** @return MuscleSegment[] */
    public function execute(Uuid $muscleId): array
    {
        $muscle = $this->muscleDAL->getById($muscleId);
        if (!$muscle) {
            throw new EntityNotFoundException();
        }

        return $this->muscleSegmentDAL->getByMuscleId($muscleId);
    }
}
