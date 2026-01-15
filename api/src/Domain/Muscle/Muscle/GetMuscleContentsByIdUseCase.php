<?php

namespace App\Domain\Muscle\Muscle;

use App\Domain\Muscle\Entity\Muscle;
use App\Domain\Muscle\Ports\MuscleDALInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityNotFoundException;

class GetMuscleContentsByIdUseCase
{
    public function __construct(
        private readonly MuscleDALInterface $muscleDAL,
    ) {

    }

    public function execute(GetMuscleByIdDTOInterface $dto): Collection
    {
        $muscle = $this->muscleDAL->getById($dto->getId());
        if (!$muscle instanceof Muscle) {
            throw new EntityNotFoundException();
        }

        return $muscle->getContents();
    }
}

