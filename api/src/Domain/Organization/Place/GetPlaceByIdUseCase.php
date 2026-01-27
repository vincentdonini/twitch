<?php

namespace App\Domain\Organization\Place;

use App\Domain\Organization\Entity\Place;
use App\Domain\Organization\Ports\PlaceDALInterface;
use Doctrine\ORM\EntityNotFoundException;

readonly class GetPlaceByIdUseCase
{
    public function __construct(
        private PlaceDALInterface $placeDAL,
    ) {

    }

    public function execute(GetPlaceByIdDTOInterface $dto): Place
    {
        $place = $this->placeDAL->getById($dto->getId());
        if (!$place instanceof Place) {
            throw new EntityNotFoundException();
        }

        return $place;
    }
}

