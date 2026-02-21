<?php

namespace App\Domain\Achievement\AchievementCategory;

use App\Domain\Achievement\Ports\AchievementCategoryDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use InvalidArgumentException;

final readonly class ListAchievementCategoryUseCase
{
    public function __construct(
        private AchievementCategoryDALInterface $achievementCategoryDAL,
    ) {
    }

    public function execute(ListAchievementCategoryDTOInterface $dto): LightPaginator
    {
        if ($dto->getPage() && $dto->getPage() < 0) {
            throw new InvalidArgumentException();
        }

        return $this->achievementCategoryDAL->listAchievementCategories(
            page   : $dto->getPage(),
            limit  : $dto->getLimit(),
            filters: $dto->getFilters(),
            sorts  : $dto->getSorts(),
        );
    }
}
