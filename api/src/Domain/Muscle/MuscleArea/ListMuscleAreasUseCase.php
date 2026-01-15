<?php

namespace App\Domain\Muscle\MuscleArea;

use App\Domain\Muscle\Ports\MuscleAreaDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use InvalidArgumentException;

class ListMuscleAreasUseCase
{
    public function __construct(
        private readonly MuscleAreaDALInterface $muscleAreaDAL,
    ) {

    }

    public function execute(ListMuscleAreasDTOInterface $dto): LightPaginator
    {
        if ($dto->getPage() && $dto->getPage() < 0) {
            throw new InvalidArgumentException();
        }

        return $this->muscleAreaDAL->listMuscleAreas(
            page   : $dto->getPage(),
            limit  : $dto->getLimit(),
            filters: $dto->getFilters(),
            sorts  : $dto->getSorts(),
        );
    }
}

