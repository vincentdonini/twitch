<?php

namespace App\Domain\Wod\Service;

use App\Application\Common\CollectionMapper;
use App\Application\Wod\DTO\WodScoreDTO;
use App\Domain\User\Service\UserService;
use App\Domain\Wod\Entity\WodScore;
use App\Domain\Wod\Leaderboard\RankedWodScore;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Filters\FilterCollection;

final readonly class WodScoreService
{
    use LocaleTrait;

    public function __construct(
        private UserService       $userService,
        private WodService        $wodService,
        private WodVariantService $wodVariantService,
    ) {
    }

    public function transformToDTO(WodScore $wodScore, FilterCollection $filters = null): WodScoreDTO
    {
        $user = $this->userService->transformToDTO(
            $wodScore->getUser(),
            $filters
        );

        $wod = $this->wodService->transformToDTO(
            $wodScore->getWod(),
            $filters
        );

        $wodVariant = $this->wodVariantService->transformToDTO(
            $wodScore->getWodVariant(),
            $filters
        );

        return new WodScoreDTO(
            id         : $wodScore->getId(),
            user       : $user,
            wod        : $wod,
            variant    : $wodVariant,
            time       : $wodScore->getTime(),
            repetitions: $wodScore->getRepetitions(),
            weight     : $wodScore->getWeight(),
            performedAt: $wodScore->getPerformedAt(),
            notes      : $wodScore->getNotes(),
            private    : $wodScore->isPrivate()
        );
    }

    public function transformCollectionToDTO(array $wodScores, FilterCollection $filters = null): array
    {
        return CollectionMapper::mapAndFilter(
            $wodScores,
            fn(WodScore $wodScore) => $this->transformToDTO($wodScore, $filters)
        );
    }

    public function transformLeaderboardToDTO(array $rankedScores, FilterCollection $filters = null): array
    {
        return CollectionMapper::mapAndFilter(
            $rankedScores,
            function (RankedWodScore $entry) use ($filters) {
                $dto       = $this->transformToDTO($entry->score, $filters);
                $dto->rank = $entry->rank;
                return $dto;
            }
        );
    }
}