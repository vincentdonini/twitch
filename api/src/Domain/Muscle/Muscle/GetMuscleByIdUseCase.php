<?php

namespace App\Domain\Muscle\Muscle;

use App\Domain\Muscle\Entity\Muscle;
use App\Domain\Muscle\Ports\MuscleDALInterface;
use Doctrine\ORM\EntityNotFoundException;

class GetMuscleByIdUseCase
{
    public function __construct(
        private readonly MuscleDALInterface $muscleDAL,
    ) {
    }

    public function execute(GetMuscleByIdDTOInterface $dto): Muscle
    {
        $muscle = $this->muscleDAL->getById($dto->getId());
        if(!$muscle instanceof Muscle){
            throw new EntityNotFoundException();
        }

        return $muscle;
    }
}

