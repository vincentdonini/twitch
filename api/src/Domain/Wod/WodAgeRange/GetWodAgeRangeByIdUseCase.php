<?php

namespace App\Domain\Wod\WodAgeRange;

use App\Domain\Wod\Entity\WodAgeRange;
use App\Domain\Wod\Ports\WodAgeRangeDALInterface;
use Doctrine\ORM\EntityNotFoundException;

class GetWodAgeRangeByIdUseCase
{
    public function __construct(
        private readonly WodAgeRangeDALInterface $wodTypeDAL,
    )
    {

    }

    public function execute(GetWodAgeRangeByIdDTOInterface $dto): WodAgeRange
    {
        $wodType = $this->wodTypeDAL->getById($dto->getId());
        if(!$wodType instanceof WodAgeRange){
            throw new EntityNotFoundException();
        }

        return $wodType;
    }
}

