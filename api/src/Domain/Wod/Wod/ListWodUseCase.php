<?php

namespace App\Domain\Wod\Wod;

use App\Domain\Wod\Ports\WodDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use InvalidArgumentException;

readonly class ListWodUseCase
{
    public function __construct(
        private WodDALInterface $wodDAL,
    ) {
    }

    public function execute(ListWodDTOInterface $dto): LightPaginator
    {
        if ($dto->getPage() && $dto->getPage() < 0) {
            throw new InvalidArgumentException();
        }

        return $this->wodDAL->listWods(
            page   : $dto->getPage(),
            limit  : $dto->getLimit(),
            filters: $dto->getFilters(),
            sorts  : $dto->getSorts(),
        );
    }
}

