<?php

namespace App\Domain\Organization\Place;

use App\Domain\Organization\Ports\PlaceDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use InvalidArgumentException;

readonly class ListPlaceUseCase
{
    public function __construct(
        private PlaceDALInterface $placeDAL,
    ) {
    }

    public function execute(ListPlaceDTOInterface $dto): LightPaginator
    {
        if ($dto->getPage() && $dto->getPage() < 0) {
            throw new InvalidArgumentException();
        }

        return $this->placeDAL->listPlaces(
            page   : $dto->getPage(),
            limit  : $dto->getLimit(),
            filters: $dto->getFilters(),
            sorts  : $dto->getSorts(),
        );
    }
}

