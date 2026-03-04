<?php

namespace App\Domain\Organization\Service;

use App\Application\Common\CollectionMapper;
use App\Application\Organization\DTO\FormulaDTO;
use App\Domain\Organization\Entity\Formula;
use App\Infrastructure\Filters\FilterCollection;

final readonly class FormulaService
{
    public function __construct(
        private PlaceService $placeService,
    ) {
    }

    public function transformToDTO(Formula $formula, FilterCollection $filters = null): FormulaDTO
    {
        $placeDTO = $this->placeService->transformToDTO($formula->getPlace(), $filters);

        return new FormulaDTO(
            id                        : $formula->getId(),
            place                     : $placeDTO,
            title                     : $formula->getTitle(),
            description               : $formula->getDescription(),
            isPublic                  : $formula->isPublic(),
            status                    : $formula->getStatus(),
            price                     : $formula->getPrice(),
            currency                  : $formula->getCurrency(),
            type                      : $formula->getType(),
            billingPeriod             : $formula->getBillingPeriod(),
            engagementDurationInMonths: $formula->getEngagementDurationInMonths(),
            cancellationNoticeInDays  : $formula->getCancellationNoticeInDays(),
            maxSessionsPerDay         : $formula->getMaxSessionsPerDay(),
            maxSessionsPerWeek        : $formula->getMaxSessionsPerWeek(),
            maxSessionsPerMonth       : $formula->getMaxSessionsPerMonth(),
            totalSessions             : $formula->getTotalSessions(),
            validityInDays            : $formula->getValidityInDays(),
            minAge                    : $formula->getMinAge(),
            maxAge                    : $formula->getMaxAge(),
            createdAt                 : $formula->getCreatedAt(),
            updatedAt                 : $formula->getUpdatedAt(),
        );
    }

    public function transformCollectionToDTO(array $formulas, FilterCollection $filters = null): array
    {
        return CollectionMapper::mapAndFilter(
            $formulas,
            fn(Formula $formula) => $this->transformToDTO($formula, $filters)
        );
    }
}
