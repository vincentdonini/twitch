<?php

namespace App\Domain\Wod\WodType;

use App\Domain\Wod\Ports\WodTypeDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use InvalidArgumentException;

final readonly class ListWodTypesUseCase
{
    public function __construct(
        private WodTypeDALInterface $wodTypeDAL,
    ) {
    }

    public function execute(ListWodTypesDTOInterface $dto): LightPaginator
    {
        if ($dto->getPage() && $dto->getPage() < 0) {
            throw new InvalidArgumentException();
        }

        return $this->wodTypeDAL->listWodTypes(
            page   : $dto->getPage(),
            limit  : $dto->getLimit(),
            filters: $dto->getFilters(),
        );
    }
}

