<?php

namespace App\Domain\Wod\WodScore;

use App\Domain\User\Entity\User;
use App\Domain\Wod\Ports\WodScoreDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use InvalidArgumentException;

final readonly class ListWodScoresUseCase
{
    public function __construct(
        private WodScoreDALInterface $wodScoreDAL,
    ) {
    }

    public function execute(
        ListWodScoresDTOInterface $dto,
        ?User                     $currentUser,
    ): LightPaginator {
        if ($dto->getPage() && $dto->getPage() < 0) {
            throw new InvalidArgumentException();
        }

        return $this->wodScoreDAL->listWodScores(
            page       : $dto->getPage(),
            limit      : $dto->getLimit(),
            filters    : $dto->getFilters(),
            currentUser: $currentUser,
        );
    }
}

