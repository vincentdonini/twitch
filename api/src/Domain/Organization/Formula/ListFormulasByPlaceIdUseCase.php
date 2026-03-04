<?php

namespace App\Domain\Organization\Formula;

use App\Domain\Organization\Ports\FormulaDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use InvalidArgumentException;

readonly class ListFormulasByPlaceIdUseCase
{
    public function __construct(
        private FormulaDALInterface $formulaDAL,
    ) {
    }

    public function execute(ListFormulasByPlaceIdDTOInterface $dto): LightPaginator
    {
        if ($dto->getPage() && $dto->getPage() < 0) {
            throw new InvalidArgumentException();
        }

        return $this->formulaDAL->listFormulasByPlaceId(
            placeId: $dto->getPlaceId(),
            page   : $dto->getPage(),
            limit  : $dto->getLimit(),
            filters: $dto->getFilters(),
            sorts  : $dto->getSorts(),
        );
    }
}
