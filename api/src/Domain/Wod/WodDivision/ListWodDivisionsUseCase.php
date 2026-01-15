<?php

namespace App\Domain\Wod\WodDivision;

use App\Domain\Wod\Ports\WodDivisionDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use InvalidArgumentException;

class ListWodDivisionsUseCase
{
    public function __construct(
        private readonly WodDivisionDALInterface $wodDivisionDAL,
    ) {
    }

    public function execute(ListWodDivisionsDTOInterface $dto): LightPaginator
    {
        if ($dto->getPage() && $dto->getPage() < 0) {
            throw new InvalidArgumentException();
        }

        return $this->wodDivisionDAL->listWodDivisions(
            page   : $dto->getPage(),
            limit  : $dto->getLimit(),
            filters: $dto->getFilters(),
        );
    }
}

