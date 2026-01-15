<?php

namespace App\Domain\Muscle\Muscle;

use App\Domain\Muscle\Ports\MuscleDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use InvalidArgumentException;

class ListMusclesUseCase
{
    public function __construct(
        private readonly MuscleDALInterface $muscleDAL,
    ) {
    }

    public function execute(ListMusclesDTOInterface $dto): LightPaginator
    {
        if ($dto->getPage() && $dto->getPage() < 0) {
            throw new InvalidArgumentException();
        }

        return $this->muscleDAL->listMuscles(
            page   : $dto->getPage(),
            limit  : $dto->getLimit(),
            filters: $dto->getFilters(),
            sorts  : $dto->getSorts(),
        );
    }
}

