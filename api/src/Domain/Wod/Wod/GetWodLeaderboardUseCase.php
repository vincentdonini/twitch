<?php

namespace App\Domain\Wod\Wod;

use App\Domain\User\Entity\User;
use App\Domain\Core\Exceptions\EntityNotFoundException;
use App\Domain\Wod\Entity\Wod;
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
        if (!$wod instanceof Wod) {
            throw new EntityNotFoundException();
        }

        $hasTimeCap = $this->variantHasTimeCap($wod, $dto);

        return $this->wodScoreDAL->getLeaderboard(
            wodId        : $dto->getWodId(),
            wodDivisionId: $dto->getWodDivisionId(),
            gender       : $dto->getGender(),
            ordering     : LeaderboardOrdering::fromWod($wod, $hasTimeCap),
            page         : $dto->getPage(),
            limit        : $dto->getLimit(),
            filters      : $dto->getFilters(),
            currentUser  : $currentUser,
        );
    }

    private function variantHasTimeCap(Wod $wod, GetWodLeaderboardDTOInterface $dto): bool
    {
        $divisionId = $dto->getWodDivisionId()->toRfc4122();
        $gender     = $dto->getGender();

        foreach ($wod->getWodVariants() as $variant) {
            if (
                $variant->getWodDivision()->getId()->toRfc4122() === $divisionId
                && $variant->getGender()?->value === $gender
                && $variant->getTimeCap() !== null
            ) {
                return true;
            }
        }

        return false;
    }
}
