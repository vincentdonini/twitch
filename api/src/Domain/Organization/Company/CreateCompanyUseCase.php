<?php

namespace App\Domain\Organization\Company;

use App\Domain\Core\Exceptions\AlreadyExistException;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Geo\Ports\CityDALInterface;
use App\Domain\Organization\Entity\Company;
use App\Domain\Organization\Enum\CompanyStatusEnum;
use App\Domain\Organization\Ports\CompanyDALInterface;
use App\Shared\Utils\StringHelper;
use InvalidArgumentException;

final readonly class CreateCompanyUseCase
{
    public function __construct(
        private DatabaseInterface   $database,
        private CityDALInterface    $cityDAL,
        private CompanyDALInterface $companyDAL,
    ) {
    }

    public function execute(CreateCompanyDTOInterface $dto): Company
    {
        if (!$this->validatePayload($dto)) {
            throw new InvalidPayloadException();
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

        $city = $this->cityDAL->findOneBy([
            'slug' => StringHelper::slugify($dto->getCity()),
        ]);

        if (!$city) {
            throw new InvalidPayloadException();
        }

        if ($dto->getPostalCode() && !$city->hasPostalCode($dto->getPostalCode())) {
            throw new InvalidPayloadException();
        }

        $company = new Company(
            slug      : StringHelper::slugify($dto->getName()),
            name      : $dto->getName(),
            legalName : $dto->getLegalName(),
            address   : $dto->getAddress(),
            postalCode: $dto->getPostalCode(),
            city      : $city,
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
            $status = CompanyStatusEnum::from($dto->getStatus());
            $company->setStatus($status);
        } catch (\Exception) {
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
            !$dto->getCity()
        ) {
            return false;
        }
        return true;
    }

    private function validateDuplicate(string $field, string $value): bool
    {
        $existingCompany = match ($field) {
            'name'      => $this->companyDAL->findOneBy(['name' => $value]),
            'legalName' => $this->companyDAL->findOneBy(['legalName' => $value]),
            'siren'     => $this->companyDAL->findOneBy(['siren' => $value]),
            'vatNumber' => $this->companyDAL->findOneBy(['vatNumber' => $value]),
            default     => throw new InvalidArgumentException("Unknown field $field"),
        };

        return $existingCompany === null;
    }
}
