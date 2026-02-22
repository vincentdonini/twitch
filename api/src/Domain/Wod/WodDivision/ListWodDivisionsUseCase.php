<?php

namespace App\Domain\Wod\WodDivision;

use App\Domain\Wod\Ports\WodDivisionDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use InvalidArgumentException;

final readonly class ListWodDivisionsUseCase
{
    public function __construct(
        private WodDivisionDALInterface $wodDivisionDAL,
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

