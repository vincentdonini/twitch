<?php

namespace App\Domain\Wod\WodDivision;

use App\Domain\Wod\Entity\WodDivision;
use App\Domain\Wod\Ports\WodDivisionDALInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityNotFoundException;

final readonly class GetWodDivisionContentsByIdUseCase
{
    public function __construct(
        private WodDivisionDALInterface $wodDivisionDAL,
    ) {
    }

    public function execute(GetWodDivisionByIdDTOInterface $dto): Collection
    {
        $wodDivision = $this->wodDivisionDAL->getById($dto->getId());
        if (!$wodDivision instanceof WodDivision) {
            throw new EntityNotFoundException();
        }

        return $wodDivision->getContents();
    }
}

