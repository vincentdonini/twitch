<?php

namespace App\Domain\Organization\Place;

use App\Domain\Core\Exceptions\AlreadyExistException;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Geo\Enum\CountryEnum;
use App\Domain\Organization\Entity\Place;
use App\Domain\Organization\Enum\CompanyStatusEnum;
use App\Domain\Organization\Ports\CompanyDALInterface;
use App\Domain\Organization\Ports\PlaceDALInterface;
use InvalidArgumentException;

final readonly class CreatePlaceUseCase
{
    public function __construct(
        private DatabaseInterface   $database,
        private PlaceDALInterface   $placeDAL,
        private CompanyDALInterface $companyDAL,
    ) {
    }

    public function execute(CreatePlaceDTOInterface $dto): Place
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

        if ($dto->getSiret() !== null && !$this->validateDuplicate('siret', $dto->getSiret())) {
            throw new AlreadyExistException();
        }

        $company = $this->companyDAL->getById($dto->getCompanyId());
        if (!$company) {
            throw new InvalidPayloadException();
        }

        try {
            $country = CountryEnum::from($dto->getCountry());
        } catch (\Exception $exception) {
            throw new InvalidPayloadException();
        }

        $place = new Place(
            company   : $company,
            slug      : $dto->getSlug(),
            name      : $dto->getName(),
            legalName : $dto->getLegalName(),
            address   : $dto->getAddress(),
            postalCode: $dto->getPostalCode(),
            city      : $dto->getCity(),
            country   : $country,
        );

        $place->setSiret($dto->getSiret() ?? null);
        $place->setAddress2($dto->getAddress2() ?? null);
        $place->setNbDaysBeforeReservation($dto->getNbDaysBeforeReservation() ?? null);
        $place->setNbHoursBeforeCancelReservation($dto->getNbHoursBeforeCancelReservation() ?? null);
        $place->setLat($dto->getLat() ?? null);
        $place->setLng($dto->getLng() ?? null);
        $place->setPhone($dto->getPhone() ?? null);
        $place->setEmail($dto->getEmail() ?? null);
        $place->setWebsite($dto->getWebsite() ?? null);
        $place->setRegistrationDate($dto->getRegistrationDate() ?? null);

        try {
            $statusEnum = CompanyStatusEnum::from($dto->getStatus());
            $company->setStatus($statusEnum);
        } catch (\Exception $exception) {
            throw new InvalidPayloadException();
        }

        $this->database->preSave($place);
        $this->database->save();

        return $place;
    }

    private function validatePayload(CreatePlaceDTOInterface $dto): bool
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
            'slug'      => $this->placeDAL->findOneBy(['slug' => $value]),
            'name'      => $this->placeDAL->findOneBy(['name' => $value]),
            'legalName' => $this->placeDAL->findOneBy(['legalName' => $value]),
            'siret'     => $this->placeDAL->findOneBy(['siret' => $value]),
            default     => throw new InvalidArgumentException(),
        };

        return $existingCompany === null;
    }
}
