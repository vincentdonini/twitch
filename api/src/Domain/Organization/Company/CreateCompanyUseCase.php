<?php

namespace App\Domain\Organization\Company;

use App\Domain\Core\Exceptions\AlreadyExistException;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Geo\Enum\CountryEnum;
use App\Domain\Organization\Entity\Company;
use App\Domain\Organization\Enum\CompanyStatusEnum;
use App\Domain\Organization\Ports\CompanyDALInterface;
use InvalidArgumentException;

final readonly class CreateCompanyUseCase
{
    public function __construct(
        private DatabaseInterface   $database,
        private CompanyDALInterface $companyDAL,
    ) {
    }

    public function execute(CreateCompanyDTOInterface $dto): Company
    {
        if (!$this->validatePayload($dto)) {
            throw new InvalidPayloadException();
        }

        if (!$this->validateDuplicate('slug', $dto->getSlug())) {
            throw new AlreadyExistException();
        }

        if (!$this->validateDuplicate('name', $dto->getName())) {
            throw new AlreadyExistException();
        }

        if (!$this->validateDuplicate('legalName', $dto->getLegalName())) {
            throw new AlreadyExistException();
        }

        if ($dto->getSiren() !== null && !$this->validateDuplicate('siren', $dto->getSiren())) {
            throw new AlreadyExistException();
        }

        try {
            $country = CountryEnum::from($dto->getCountry());
        } catch (\Exception $exception) {
            throw new InvalidPayloadException();
        }

        $company = new Company(
            slug      : $dto->getSlug(),
            name      : $dto->getName(),
            legalName : $dto->getLegalName(),
            address   : $dto->getAddress(),
            postalCode: $dto->getPostalCode(),
            city      : $dto->getCity(),
            country   : $country,
        );

        $company->setSiren($dto->getSiren() ?? null);
        $company->setVatNumber($dto->getVatNumber() ?? null);
        $company->setLegalForm($dto->getLegalForm() ?? null);
        $company->setActivityCode($dto->getActivityCode() ?? null);
        $company->setAddress2($dto->getAddress2() ?? null);
        $company->setPhone($dto->getPhone() ?? null);
        $company->setEmail($dto->getEmail() ?? null);
        $company->setRegistrationDate($dto->getRegistrationDate() ?? null);

        try {
            $statusEnum = CompanyStatusEnum::from($dto->getStatus());
            $company->setStatus($statusEnum);
        } catch (\Exception $exception) {
            throw new InvalidPayloadException();
        }

        $this->database->preSave($company);
        $this->database->save();

        return $company;
    }

    private function validatePayload(CreateCompanyDTOInterface $dto): bool
    {
        if (
            !$dto->getName() ||
            !$dto->getLegalName() ||
            !$dto->getAddress() ||
            !$dto->getPostalCode() ||
            !$dto->getCity() ||
            !$dto->getCountry()
        ) {
            return false;
        }
        return true;
    }

    private function validateDuplicate(string $field, string $value): bool
    {
        $existingCompany = match ($field) {
            'slug'      => $this->companyDAL->findOneBy(['slug' => $value]),
            'name'      => $this->companyDAL->findOneBy(['name' => $value]),
            'legalName' => $this->companyDAL->findOneBy(['legalName' => $value]),
            'siren'     => $this->companyDAL->findOneBy(['siren' => $value]),
            'vatNumber' => $this->companyDAL->findOneBy(['vatNumber' => $value]),
            default     => throw new InvalidArgumentException("Unknown field $field"),
        };

        return $existingCompany === null;
    }
}
