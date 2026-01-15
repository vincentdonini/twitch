<?php

namespace App\Domain\Wod\WodCategory;

use App\Domain\Wod\Ports\WodCategoryDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use InvalidArgumentException;

class ListWodCategoriesUseCase
{
    public function __construct(
        private readonly WodCategoryDALInterface $wodCategoryDAL,
    ) {
    }

    public function execute(ListWodCategoriesDTOInterface $dto): LightPaginator
    {
        if ($dto->getPage() && $dto->getPage() < 0) {
            throw new InvalidArgumentException();
        }

        return $this->wodCategoryDAL->listWodCategories(
            page   : $dto->getPage(),
            limit  : $dto->getLimit(),
            filters: $dto->getFilters(),
        );
    }
}

