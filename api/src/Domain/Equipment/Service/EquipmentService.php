<?php

namespace App\Domain\Equipment\Service;

use App\Application\Common\CollectionMapper;
use App\Application\Equipment\DTO\EquipmentDTO;
use App\Domain\Common\Filter\EntityFieldFilter;
use App\Domain\Equipment\Entity\Equipment;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Filters\FilterCollection;
use Symfony\Component\HttpFoundation\RequestStack;

class EquipmentService
{
    use LocaleTrait;

    private array $filterMapping;

    public function __construct(
        private readonly RequestStack      $requestStack,
        private readonly EntityFieldFilter $EntityFieldFilter,
    ) {
        $this->filterMapping = [
            'contents.title'   => fn(Equipment $equipment) => $equipment->getContentByLocale($this->getLocale())?->getTitle(),
            'contents.summary' => fn(Equipment $equipment) => $equipment->getContentByLocale($this->getLocale())?->getSummary(),
            'contents.details' => fn(Equipment $equipment) => $equipment->getContentByLocale($this->getLocale())?->getDetails(),
        ];
    }

    public function transformToDTO(Equipment $equipment, FilterCollection $filters = null, int $wodCount = 0): ?EquipmentDTO
    {
        $content = $equipment->getContentByLocale($this->getLocale());
        if (!$this->EntityFieldFilter->match($equipment, $filters, $this->filterMapping)) {
            return null;
        }

        return new EquipmentDTO(
            id      : $equipment->getId(),
            slug    : $equipment->getSlug(),
            title   : $content ? $content->getTitle() : '',
            summary : $content ? $content->getSummary() : '',
            details : $content ? $content->getDetails() : '',
            wodCount: $wodCount,
        );
    }

    public function transformCollectionToDTO(array $equipments, FilterCollection $filters = null, array $wodCounts = []): array
    {
        return CollectionMapper::mapAndFilter(
            $equipments,
            fn(Equipment $equipment) => $this->transformToDTO($equipment, $filters, $wodCounts[(string)$equipment->getId()] ?? 0)
        );
    }
}
