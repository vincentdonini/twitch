<?php

namespace App\Domain\Wod\WodDivision;

use App\Domain\Wod\Entity\WodDivision;
use App\Domain\Wod\Ports\WodDivisionDALInterface;
use Doctrine\ORM\EntityNotFoundException;

final readonly class GetWodDivisionByIdUseCase
{
    public function __construct(
        private WodDivisionDALInterface $wodDivisionDAL,
    ) {
    }

    public function execute(GetWodDivisionByIdDTOInterface $dto): WodDivision
    {
        $wodDivision = $this->wodDivisionDAL->getById($dto->getId());
        if (!$wodDivision instanceof WodDivision) {
            throw new EntityNotFoundException();
        }

        return $wodDivision;
    }
}

