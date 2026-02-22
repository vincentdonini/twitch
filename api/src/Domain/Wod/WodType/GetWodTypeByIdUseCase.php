<?php

namespace App\Domain\Wod\WodType;

use App\Domain\Wod\Entity\WodType;
use App\Domain\Wod\Ports\WodTypeDALInterface;
use Doctrine\ORM\EntityNotFoundException;

final readonly class GetWodTypeByIdUseCase
{
    public function __construct(
        private WodTypeDALInterface $wodTypeDAL,
    ) {
    }

    public function execute(GetWodTypeByIdDTOInterface $dto): WodType
    {
        $wodType = $this->wodTypeDAL->getById($dto->getId());
        if (!$wodType instanceof WodType) {
            throw new EntityNotFoundException();
        }

        return $wodType;
    }
}
