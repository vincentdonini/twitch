<?php

namespace App\UI\Adapters\Http\Achievement\UserAchievementProgress;

use App\Domain\Achievement\UserAchievementProgress\CompareUserAchievementsDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class CompareUserAchievementsHttp implements CompareUserAchievementsDTOInterface
{
    public function __construct(
        private Uuid $userId,
        private Uuid $otherId,
    ) {

    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getOtherId(): Uuid
    {
        return $this->otherId;
    }
}
