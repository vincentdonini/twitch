<?php

namespace App\Domain\Achievement\AchievementGroup;

use App\Domain\Achievement\Ports\AchievementGroupDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use InvalidArgumentException;

final readonly class ListAchievementGroupUseCase
{
    public function __construct(
        private AchievementGroupDALInterface $achievementGroupDAL,
    ) {
    }

    public function execute(ListAchievementGroupDTOInterface $dto): LightPaginator
    {
        if ($dto->getPage() && $dto->getPage() < 0) {
            throw new InvalidArgumentException();
        }

        return $this->achievementGroupDAL->listAchievementGroups(
            page   : $dto->getPage(),
            limit  : $dto->getLimit(),
            filters: $dto->getFilters(),
            sorts  : $dto->getSorts(),
        );
    }
}
