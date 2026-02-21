<?php

namespace App\Domain\Achievement\Service;

use App\Application\Achievement\DTO\AchievementLevelDTO;
use App\Application\Common\CollectionMapper;
use App\Domain\Achievement\Entity\AchievementLevel;
use App\Domain\Achievement\Enum\AchievementRarityEnum;
use App\Domain\User\Ports\UserDALInterface;
use App\Infrastructure\Filters\FilterCollection;

final readonly class AchievementLevelService
{
    public function __construct(
        private UserDALInterface $userDAL,
    ) {
    }

    public function transformToDTO(AchievementLevel $achievementLevel, FilterCollection $filters = null): ?AchievementLevelDTO
    {
        $unlockRatio = null;
        $rarity     = null;

        $totalUsers = $this->userDAL->countUsers();

        if ($totalUsers > 0) {
            $unlockRatio = round(
                $achievementLevel->getUnlockCount() / $totalUsers,
                4
            );

            $rarity = AchievementRarityEnum::fromRatio($unlockRatio);
        }

        return new AchievementLevelDTO(
            id         : $achievementLevel->getId(),
            level      : $achievementLevel->getLevel(),
            unlockRatio: $unlockRatio,
            rarity     : $rarity,
        );
    }

    public function transformCollectionToDTO(array $achievementCategories, FilterCollection $filters = null): array
    {
        return CollectionMapper::mapAndFilter(
            $achievementCategories,
            fn(AchievementLevel $achievementLevel) => $this->transformToDTO($achievementLevel, $filters)
        );
    }
}