<?php

namespace App\Domain\Muscle\MuscleSegment;

use App\Domain\Muscle\Entity\MuscleSegment;
use App\Domain\Muscle\Ports\MuscleSegmentDALInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Uid\Uuid;

final readonly class GetMuscleSegmentContentsByIdUseCase
{
    public function __construct(
        private MuscleSegmentDALInterface $muscleSegmentDAL,
    ) {
    }

    public function execute(Uuid $id): Collection
    {
        $segment = $this->muscleSegmentDAL->getById($id);
        if (!$segment instanceof MuscleSegment) {
            throw new EntityNotFoundException();
        }

        return $segment->getContents();
    }
}
