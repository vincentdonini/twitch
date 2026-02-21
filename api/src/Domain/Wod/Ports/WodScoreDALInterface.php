<?php

namespace App\Domain\Wod\Ports;

use App\Domain\User\Entity\User;
use App\Domain\Wod\Entity\WodScore;
use App\Domain\Wod\Leaderboard\LeaderboardOrdering;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use Symfony\Component\Uid\Uuid;

interface WodScoreDALInterface
{
    public function getById(string $id): ?WodScore;

    /* @return WodScore[] */
    public function getByUser(User $user): array;

    public function listWodScores(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?User             $currentUser = null,
    ): LightPaginator;

    public function getLeaderboard(
        string              $wodId,
        string              $wodDivisionId,
        string              $gender,
        LeaderboardOrdering $ordering,
        int                 $page = 1,
        int                 $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection   $filters = null,
        ?User               $currentUser = null,
    ): LightPaginator;

    public function countWeekendWods(User $user): int;
}
