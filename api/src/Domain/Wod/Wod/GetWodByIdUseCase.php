<?php

namespace App\Domain\Wod\Wod;

use App\Domain\Wod\Entity\Wod;
use App\Domain\Wod\Ports\WodDALInterface;
use Doctrine\ORM\EntityNotFoundException;

class GetWodByIdUseCase
{
    public function __construct(
        private readonly WodDALInterface $wodDAL,
    )
    {

    }

    public function execute(GetWodByIdDTOInterface $dto): Wod
    {
        $wod = $this->wodDAL->getById($dto->getId());
        if(!$wod instanceof Wod){
            throw new EntityNotFoundException();
        }

        return $wod;
    }
}

