<?php

namespace App\UI\Adapters\Http\Achievement\UserAchievementProgress;

use App\Domain\Achievement\UserAchievementProgress\ListUserAchievementProgressesDTOInterface;

final readonly class ListUserAchievementProgressesHttp implements ListUserAchievementProgressesDTOInterface
{
    public function __construct(
        private ?int $page = null,
        private ?int $limit = null,
    ) {

    }

    public function getPage(): int
    {
        return $this->page ?? 1;
    }

    public function getLimit(): int
    {
        return $this->limit ?? 15;
    }
}
