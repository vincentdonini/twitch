<?php

namespace App\Domain\Organization\Service;

use App\Application\Common\CollectionMapper;
use App\Application\Organization\DTO\CompanyDTO;
use App\Domain\Organization\Entity\Company;
use App\Infrastructure\Filters\FilterCollection;

final readonly class CompanyService
{
    public function transformToDTO(Company $company, FilterCollection $filters = null): ?CompanyDTO
    {
        return new CompanyDTO(
            id              : $company->getId(),
            slug            : $company->getSlug(),
            name            : $company->getName(),
            legalName       : $company->getLegalName(),
            siren           : $company->getSiren(),
            vatNumber       : $company->getVatNumber(),
            legalForm       : $company->getLegalForm(),
            activityCode    : $company->getActivityCode(),
            address         : $company->getAddress(),
            address2        : $company->getAddress2(),
            postalCode      : $company->getPostalCode(),
            city            : $company->getCity(),
            department      : $company->getCity()->getDepartment(),
            region          : $company->getCity()->getDepartment()->getRegion(),
            country         : $company->getCity()->getDepartment()->getRegion()->getCountry(),
            status          : $company->getStatus(),
            registrationDate: $company->getRegistrationDate(),
            createdAt       : $company->getCreatedAt(),
            updatedAt       : $company->getUpdatedAt(),
        );
    }

    public function transformCollectionToDTO(array $companies, FilterCollection $filters = null): array
    {
        return CollectionMapper::mapAndFilter(
            $companies,
            fn(Company $company) => $this->transformToDTO($company, $filters)
        );
    }
}