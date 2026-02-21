<?php

namespace App\Domain\Achievement\Service;

use App\Application\Achievement\DTO\AchievementGroupDTO;
use App\Application\Common\CollectionMapper;
use App\Domain\Achievement\Entity\AchievementGroup;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Filters\FilterCollection;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class AchievementGroupService
{
    use LocaleTrait;

    private array $filterMapping;

    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    public function transformToDTO(AchievementGroup $achievementGroup, FilterCollection $filters = null): ?AchievementGroupDTO
    {
        $content = $achievementGroup->getContentByLocale($this->getLocale());

        return new AchievementGroupDTO(
            id         : $achievementGroup->getId(),
            code       : $achievementGroup->getCode(),
            position   : $achievementGroup->getPosition(),
            title      : $content ? $content->getTitle() : '',
            description: $content ? $content->getDescription() : '',
        );
    }

    public function transformCollectionToDTO(array $achievementGroups, FilterCollection $filters = null): array
    {
        return CollectionMapper::mapAndFilter(
            $achievementGroups,
            fn(AchievementGroup $achievementGroup) => $this->transformToDTO($achievementGroup, $filters)
        );
    }
}