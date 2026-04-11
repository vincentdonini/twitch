<?php

namespace App\Domain\Muscle\MuscleSegment;

use App\Domain\Muscle\Entity\MuscleSegment;
use App\Domain\Muscle\Ports\MuscleSegmentDALInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Uid\Uuid;

final readonly class GetMuscleSegmentByIdUseCase
{
    public function __construct(
        private MuscleSegmentDALInterface $muscleSegmentDAL,
    ) {
    }

    public function execute(Uuid $id): MuscleSegment
    {
        $segment = $this->muscleSegmentDAL->getById($id);
        if (!$segment instanceof MuscleSegment) {
            throw new EntityNotFoundException();
        }

        return $segment;
    }
}
