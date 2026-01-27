<?php

namespace App\Domain\Organization\Place;

use App\Domain\Core\Exceptions\AlreadyExistException;
use App\Domain\Core\Exceptions\EntityNotFoundException;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Geo\Enum\CountryEnum;
use App\Domain\Organization\Entity\Place;
use App\Domain\Organization\Enum\PlaceStatusEnum;
use App\Domain\Organization\Ports\CompanyDALInterface;
use App\Domain\Organization\Ports\PlaceDALInterface;
use App\Shared\Utils\StringHelper;
use InvalidArgumentException;

final readonly class UpdatePlaceUseCase
{
    public function __construct(
        private DatabaseInterface   $database,
        private PlaceDALInterface   $placeDAL,
        private CompanyDALInterface $companyDAL,
    ) {
    }

    public function execute(UpdatePlaceDTOInterface $dto): Place
    {
        $place = $this->placeDAL->getById($dto->getId());
        if (!$place instanceof Place) {
            throw new EntityNotFoundException();
        }

        if (!empty($dto->getCompanyId())) {
            $company = $this->companyDAL->getById($dto->getCompanyId());
            if (!$company) {
                throw new InvalidPayloadException();
            }

            $place->setCompany($company);
        }

        if (!empty($dto->getName())) {
            if (!$this->validateDuplicate('name', $dto->getName(), $place)) {
                throw new AlreadyExistException();
            }

            $place->setSlug(StringHelper::slugify($dto->getName()));
            $place->setName($dto->getName());
        }

        if (!empty($dto->getLegalName())) {
            if (!$this->validateDuplicate('legalName', $dto->getLegalName(), $place)) {
                throw new AlreadyExistException();
            }

            $place->setLegalName($dto->getLegalName());
        }

        if (!empty($dto->getSiret())) {
            if (!$this->validateDuplicate('siren', $dto->getSiret(), $place)) {
                throw new AlreadyExistException();
            }

            $place->setSiret($dto->getSiret());
        }

        if (!empty($dto->getAddress())) {
            $place->setAddress($dto->getAddress());
        }

        if (!empty($dto->getAddress2())) {
            $place->setAddress($dto->getAddress2());
        }

        if (!empty($dto->getPostalCode())) {
            $place->setPostalCode($dto->getPostalCode());
        }

        if (!empty($dto->getCity())) {
            $place->setCity($dto->getCity());
        }

        if (!empty($dto->getCountry())) {
            try {
                $countryEnum = CountryEnum::from($dto->getCountry());
                $place->setCountry($countryEnum);
            } catch (\Exception $exception) {
                throw new InvalidPayloadException();
            }
        }

        if (!empty($dto->getNbDaysBeforeReservation())) {
            $place->setNbDaysBeforeReservation($dto->getNbDaysBeforeReservation());
        }

        if (!empty($dto->getNbHoursBeforeCancelReservation())) {
            $place->setNbHoursBeforeCancelReservation($dto->getNbHoursBeforeCancelReservation());
        }

        if (!empty($dto->getLat())) {
            $place->setLat($dto->getLat());
        }

        if (!empty($dto->getLng())) {
            $place->setLng($dto->getLng());
        }

        if (!empty($dto->getPhone())) {
            $place->setPhone($dto->getPhone());
        }

        if (!empty($dto->getEmail())) {
            $place->setEmail($dto->getEmail());
        }

        if (!empty($dto->getWebsite())) {
            $place->setWebsite($dto->getWebsite());
        }

        if (!empty($dto->getRegistrationDate())) {
            $place->setRegistrationDate($dto->getRegistrationDate());
        }

        if (!empty($dto->getStatus())) {
            try {
                $statusEnum = PlaceStatusEnum::from($dto->getStatus());
                $place->setStatus($statusEnum);
            } catch (\Exception $exception) {
                throw new InvalidPayloadException();
            }
        }

        $this->database->preSave($place);
        $this->database->save();

        return $place;
    }

    private function validateDuplicate(string $field, string $value, Place $currentPlace): bool
    {
        $existingPlace = match ($field) {
//            'slug'      => $this->placeDAL->findOneBy(['slug' => $value]),
            'name'      => $this->placeDAL->findOneBy(['name' => $value]),
            'legalName' => $this->placeDAL->findOneBy(['legalName' => $value]),
            'siret'     => $this->placeDAL->findOneBy(['siret' => $value]),
            default     => throw new InvalidArgumentException(),
        };

        return $existingPlace === null || $existingPlace->getId() === $currentPlace->getId();
    }
}
