<?php

namespace App\Domain\Achievement\Achievement;

use App\Domain\Achievement\Ports\AchievementDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use InvalidArgumentException;

final readonly class ListAchievementUseCase
{
    public function __construct(
        private AchievementDALInterface $achievementDAL,
    ) {
    }

    public function execute(ListAchievementDTOInterface $dto): LightPaginator
    {
        if ($dto->getPage() && $dto->getPage() < 0) {
            throw new InvalidArgumentException();
        }

        return $this->achievementDAL->listAchievements(
            page   : $dto->getPage(),
            limit  : $dto->getLimit(),
            filters: $dto->getFilters(),
            sorts  : $dto->getSorts(),
        );
    }
}
