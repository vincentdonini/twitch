<?php

namespace App\Domain\Wod\WodCategory;

use App\Domain\Wod\Entity\WodCategory;
use App\Domain\Wod\Ports\WodCategoryDALInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityNotFoundException;

readonly class GetWodCategoryContentsByIdUseCase
{
    public function __construct(
        private WodCategoryDALInterface $wodCategoryDAL,
    ) {

    }

    public function execute(GetWodCategoryByIdDTOInterface $dto): Collection
    {
        $wodCategory = $this->wodCategoryDAL->getById($dto->getId());
        if (!$wodCategory instanceof WodCategory) {
            throw new EntityNotFoundException();
        }

        return $wodCategory->getContents();
    }
}

