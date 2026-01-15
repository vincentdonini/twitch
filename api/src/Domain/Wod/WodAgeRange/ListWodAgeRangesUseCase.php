<?php

namespace App\Domain\Wod\WodAgeRange;

use App\Domain\Wod\Ports\WodAgeRangeDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use InvalidArgumentException;

class ListWodAgeRangesUseCase
{
    public function __construct(
        private readonly WodAgeRangeDALInterface $wodTypeDAL,
    ) {
    }

    public function execute(ListWodAgeRangesDTOInterface $dto): LightPaginator
    {
        if ($dto->getPage() && $dto->getPage() < 0) {
            throw new InvalidArgumentException();
        }

        return $this->wodTypeDAL->listWodAgeRanges(
            page   : $dto->getPage(),
            limit  : $dto->getLimit(),
            filters: $dto->getFilters(),
        );
    }
}

