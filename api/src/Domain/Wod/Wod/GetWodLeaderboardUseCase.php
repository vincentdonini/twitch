<?php

namespace App\Domain\Wod\Wod;

use App\Domain\User\Entity\User;
use App\Domain\Wod\Leaderboard\LeaderboardOrdering;
use App\Domain\Wod\Ports\WodDALInterface;
use App\Domain\Wod\Ports\WodScoreDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;

final readonly class GetWodLeaderboardUseCase
{
    public function __construct(
        private WodDALInterface      $wodDAL,
        private WodScoreDALInterface $wodScoreDAL,
    ) {
    }

    public function execute(
        GetWodLeaderboardDTOInterface $dto,
        ?User                         $currentUser
    ): LightPaginator {
        $wod = $this->wodDAL->getById($dto->getWodId());

        return $this->wodScoreDAL->getLeaderboard(
            wodId        : $dto->getWodId(),
            wodDivisionId: $dto->getWodDivisionId(),
            gender       : $dto->getGender(),
            ordering     : LeaderboardOrdering::fromWod($wod),
            page         : $dto->getPage(),
            limit        : $dto->getLimit(),
            filters      : $dto->getFilters(),
            currentUser  : $currentUser,
        );
    }
}

