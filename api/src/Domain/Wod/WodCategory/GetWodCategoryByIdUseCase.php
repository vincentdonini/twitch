<?php

namespace App\Domain\Wod\WodCategory;

use App\Domain\Wod\Entity\WodCategory;
use App\Domain\Wod\Ports\WodCategoryDALInterface;
use App\Domain\Core\Exceptions\EntityNotFoundException;

class GetWodCategoryByIdUseCase
{
    public function __construct(
        private readonly WodCategoryDALInterface $wodCategoryDAL,
    ) {
    }

    public function execute(GetWodCategoryByIdDTOInterface $dto): WodCategory
    {
        $wodCategory = $this->wodCategoryDAL->getById($dto->getId());
        if (!$wodCategory instanceof WodCategory) {
            throw new EntityNotFoundException();
        }

        return $wodCategory;
    }
}

