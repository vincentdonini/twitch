<?php

namespace App\Domain\Achievement\Ports;

use App\Domain\Achievement\Entity\Achievement;
use App\Domain\Achievement\Entity\AchievementLevel;
use App\Domain\Achievement\Entity\UserAchievementProgress;
use App\Domain\User\Entity\User;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Paginator\RequestPaginator;
use Symfony\Component\Uid\Uuid;

interface UserAchievementProgressDALInterface
{
    public function getById(Uuid $id): ?UserAchievementProgress;

    public function listUserAchievementProgresses(
        int   $page = 1,
        int   $limit = RequestPaginator::DEFAULT_LIMIT,
        ?User $user = null,
    ): LightPaginator;

    public function findUserAchievementProgress(
        User             $user,
        Achievement      $achievement,
        AchievementLevel $achievementLevel,
    ): ?UserAchievementProgress;

    public function countByAchievementLevel(
        AchievementLevel $achievementLevel,
    ): int;

    public function countGroupedByAchievementLevel(): array;
}
