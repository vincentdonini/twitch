<?php

namespace App\Domain\Organization\Service;

use App\Application\Common\CollectionMapper;
use App\Application\Organization\DTO\PlaceDTO;
use App\Domain\Organization\Entity\Place;
use App\Infrastructure\Filters\FilterCollection;

final readonly class PlaceService
{
    public function __construct(
        private CompanyService $companyService,
    ) {
    }

    public function transformToDTO(Place $place, FilterCollection $filters = null): ?PlaceDTO
    {
        $companyDTO = $this->companyService->transformToDTO($place->getCompany(), $filters);

        return new PlaceDTO(
            id                            : $place->getId(),
            company                       : $companyDTO,
            slug                          : $place->getSlug(),
            name                          : $place->getName(),
            legalName                     : $place->getLegalName(),
            siret                         : $place->getSiret(),
            address                       : $place->getAddress(),
            address2                      : $place->getAddress2(),
            postalCode                    : $place->getPostalCode(),
            city                          : $place->getCity(),
            department                    : $place->getCity()->getDepartment(),
            region                        : $place->getCity()->getDepartment()->getRegion(),
            country                       : $place->getCity()->getDepartment()->getRegion()->getCountry(),
            status                        : $place->getStatus(),
            nbDaysBeforeReservation       : $place->getNbDaysBeforeReservation(),
            nbHoursBeforeCancelReservation: $place->getNbHoursBeforeCancelReservation(),
            phone                         : $place->getPhone(),
            email                         : $place->getEmail(),
            website                       : $place->getWebsite(),
            registrationDate              : $place->getRegistrationDate(),
            createdAt                     : $place->getCreatedAt(),
            updatedAt                     : $place->getUpdatedAt(),
        );
    }

    public function transformCollectionToDTO(array $places, FilterCollection $filters = null): array
    {
        return CollectionMapper::mapAndFilter(
            $places,
            fn(Place $place) => $this->transformToDTO($place, $filters)
        );
    }
}