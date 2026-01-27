<?php

namespace App\Domain\Organization\Company;

use App\Domain\Core\Exceptions\AlreadyExistException;
use App\Domain\Core\Exceptions\EntityNotFoundException;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Geo\Enum\CountryEnum;
use App\Domain\Organization\Entity\Company;
use App\Domain\Organization\Enum\CompanyStatusEnum;
use App\Domain\Organization\Ports\CompanyDALInterface;
use InvalidArgumentException;

final readonly class UpdateCompanyUseCase
{
    public function __construct(
        private DatabaseInterface   $database,
        private CompanyDALInterface $companyDAL,
    ) {
    }

    public function execute(UpdateCompanyDTOInterface $dto): Company
    {
        $company = $this->companyDAL->getById($dto->getId());
        if (!$company instanceof Company) {
            throw new EntityNotFoundException();
        }

        if (!empty($dto->getSlug())) {
            if (!$this->validateDuplicate('slug', $dto->getSlug(), $company)) {
                throw new AlreadyExistException();
            }

            $company->setSlug($dto->getSlug());
        }

        if (!empty($dto->getName())) {
            if ($this->validateDuplicate('name', $dto->getName(), $company)) {
                throw new AlreadyExistException();
            }

            $company->setName($dto->getName());
        }

        if (!empty($dto->getLegalName())) {
            if ($this->validateDuplicate('legalName', $dto->getLegalName(), $company)) {
                throw new AlreadyExistException();
            }

            $company->setLegalName($dto->getLegalName());
        }

        if (!empty($dto->getSiren())) {
            if ($dto->getSiren() !== null && $this->validateDuplicate('siren', $dto->getSiren(), $company)) {
                throw new AlreadyExistException();
            }

            $company->setSiren($dto->getSiren());
        }

        if (!empty($dto->getVatNumber())) {
            if ($this->validateDuplicate('vatNumber', $dto->getVatNumber(), $company)) {
                throw new AlreadyExistException();
            }

            $company->setVatNumber($dto->getVatNumber());
        }

        if (!empty($dto->getLegalForm())) {
            $company->setLegalForm($dto->getLegalForm());
        }

        if (!empty($dto->getActivityCode())) {
            $company->setActivityCode($dto->getActivityCode());
        }

        if (!empty($dto->getAddress())) {
            $company->setAddress($dto->getAddress());
        }

        if (!empty($dto->getAddress2())) {
            $company->setAddress2($dto->getAddress2());
        }

        if (!empty($dto->getPostalCode())) {
            $company->setPostalCode($dto->getPostalCode());
        }

        if (!empty($dto->getCity())) {
            $company->setCity($dto->getCity());
        }

        if (!empty($dto->getCountry())) {
            try {
                $countryEnum = CountryEnum::from($dto->getCountry());
                $company->setCountry($countryEnum);
            } catch (\Exception $exception) {
                throw new InvalidPayloadException();
            }
        }

        if (!empty($dto->getPhone())) {
            $company->setPhone($dto->getPhone());
        }

        if (!empty($dto->getEmail())) {
            $company->setEmail($dto->getEmail());
        }

        if (!empty($dto->getRegistrationDate())) {
            $company->setRegistrationDate($dto->getRegistrationDate());
        }

        if (!empty($dto->getStatus())) {
            try {
                $statusEnum = CompanyStatusEnum::from($dto->getStatus());
                $company->setStatus($statusEnum);
            } catch (\Exception $exception) {
                throw new InvalidPayloadException();
            }
        }

        $this->database->preSave($company);
        $this->database->save();

        return $company;
    }

    private function validateDuplicate(string $field, string $value, Company $currentCompany): bool
    {
        $existingCompany = match ($field) {
            'slug'      => $this->companyDAL->findOneBy(['slug' => $value]),
            'name'      => $this->companyDAL->findOneBy(['name' => $value]),
            'legalName' => $this->companyDAL->findOneBy(['legalName' => $value]),
            'siren'     => $this->companyDAL->findOneBy(['siren' => $value]),
            'vatNumber' => $this->companyDAL->findOneBy(['vatNumber' => $value]),
            default     => throw new InvalidArgumentException("Unknown field $field"),
        };

        return $existingCompany !== null && $existingCompany->getId() !== $currentCompany->getId();
    }
}
