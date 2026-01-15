<?php

namespace App\Domain\Wod\Ports;

use App\Domain\User\Entity\User;
use App\Domain\Wod\Entity\WodScore;
use App\Domain\Wod\Leaderboard\LeaderboardOrdering;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;

interface WodScoreDALInterface
{
    public function getById(string $id): ?WodScore;

    public function listWodScores(
        int               $page = 1,
        int               $limit = 15,
        ?FilterCollection $filters = null,
        ?User             $currentUser = null,
    ): LightPaginator;

    public function getLeaderboard(
        string              $wodId,
        string              $wodDivisionId,
        string              $gender,
        LeaderboardOrdering $ordering,
        int                 $page = 1,
        int                 $limit = 15,
        ?FilterCollection   $filters = null,
        ?User               $currentUser = null,
    ): LightPaginator;
}
