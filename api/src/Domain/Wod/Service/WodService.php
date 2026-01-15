<?php

namespace App\Domain\Wod\Service;

use App\Application\Common\CollectionMapper;
use App\Application\Wod\DTO\WodDTO;
use App\Domain\Wod\Entity\Wod;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Filters\FilterCollection;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class WodService
{
    use LocaleTrait;

    private array $filterMapping;

    public function __construct(
        private RequestStack       $requestStack,
        private WodTypeService     $wodTypeService,
        private WodCategoryService $wodCategoryService,
        private WodVariantService  $wodVariantService,
    ) {
    }

    public function transformToDTO(Wod $wod, FilterCollection $filters = null): ?WodDTO
    {
        $content = $wod->getContentByLocale($this->getLocale());

        $wodVariantsDTOs = $this->wodVariantService->transformCollectionToDTO(
            $wod->getWodVariants()->toArray(),
            $filters
        );

        return new WodDTO(
            id      : $wod->getId(),
            name    : $wod->getName(),
            title   : $content ? $content->getTitle() : '',
            summary : $content ? $content->getSummary() : '',
            details : $content ? $content->getDetails() : '',
            rules   : $content ? $content->getRules() : '',
            tips    : $content ? $content->getTips() : '',
            type    : $this->wodTypeService->transformToDTO($wod->getWodType(), $filters),
            category: $this->wodCategoryService->transformToDTO($wod->getWodCategory(), $filters),
            variants: $wodVariantsDTOs,
            teamSize: $wod->getTeamSize(),
        );
    }

    public function transformCollectionToDTO(array $wods, FilterCollection $filters = null): array
    {
        return CollectionMapper::mapAndFilter(
            $wods,
            fn(Wod $wod) => $this->transformToDTO($wod, $filters)
        );
    }
}