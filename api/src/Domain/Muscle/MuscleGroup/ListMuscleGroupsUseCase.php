<?php

namespace App\Domain\Muscle\MuscleGroup;

use App\Domain\Muscle\Ports\MuscleGroupDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use InvalidArgumentException;

readonly class ListMuscleGroupsUseCase
{
    public function __construct(
        private MuscleGroupDALInterface $muscleGroupDAL,
    ) {
    }

    public function execute(ListMuscleGroupsDTOInterface $dto): LightPaginator
    {
        if ($dto->getPage() && $dto->getPage() < 0) {
            throw new InvalidArgumentException();
        }

        return $this->muscleGroupDAL->listMuscleGroups(
            page   : $dto->getPage(),
            limit  : $dto->getLimit(),
            filters: $dto->getFilters(),
            sorts  : $dto->getSorts(),
        );
    }
}
