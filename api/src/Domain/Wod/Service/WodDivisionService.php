<?php

namespace App\Domain\Wod\Service;

use App\Application\Common\CollectionMapper;
use App\Application\Wod\DTO\WodDivisionDTO;
use App\Domain\Wod\Entity\WodDivision;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Filters\FilterCollection;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class WodDivisionService
{
    use LocaleTrait;

    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    public function transformToDTO(WodDivision $wodDivision, FilterCollection $filters = null): ?WodDivisionDTO
    {
        $content = $wodDivision->getContentByLocale($this->getLocale());

        return new WodDivisionDTO(
            id     : $wodDivision->getId(),
            slug   : $wodDivision->getSlug(),
            title  : $content ? $content->getTitle() : '',
            summary: $content ? $content->getSummary() : '',
            details: $content ? $content->getDetails() : '',
        );
    }

    public function transformCollectionToDTO(array $wodDivisions, FilterCollection $filters = null): array
    {
        return CollectionMapper::mapAndFilter(
            $wodDivisions,
            fn(WodDivision $wodDivision) => $this->transformToDTO($wodDivision, $filters)
        );
    }
}