<?php

namespace App\Domain\Organization\Place;

use App\Domain\Core\Exceptions\AlreadyExistException;
use App\Domain\Core\Exceptions\EntityNotFoundException;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Geo\Ports\CityDALInterface;
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
        private CityDALInterface    $cityDAL,
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
            if (!$this->validateDuplicate('siret', $dto->getSiret(), $place)) {
                throw new AlreadyExistException();
            }

            $place->setSiret($dto->getSiret());
        }

        if (!empty($dto->getAddress())) {
            $place->setAddress($dto->getAddress());
        }

        if (!empty($dto->getAddress2())) {
            $place->setAddress2($dto->getAddress2());
        }

        $targetCity = $place->getCity();

        if ($dto->getCity()) {
            $targetCity = $this->cityDAL->findOneBy([
                'slug' => StringHelper::slugify($dto->getCity()),
            ]);

            if (!$targetCity) {
                throw new InvalidPayloadException();
            }
        }

        $targetPostalCode = $dto->getPostalCode() ?? $place->getPostalCode();

        if ($targetPostalCode && !$targetCity->hasPostalCode($targetPostalCode)) {
            throw new InvalidPayloadException();
        }

        if ($dto->getCity()) {
            $place->setCity($targetCity);
        }

        if ($dto->getPostalCode()) {
            $place->setPostalCode($targetPostalCode);
        }

        if (!empty($dto->getNbDaysBeforeReservation())) {
            $place->setNbDaysBeforeReservation($dto->getNbDaysBeforeReservation());
        }

        if (!empty($dto->getNbHoursBeforeCancelReservation())) {
            $place->setNbHoursBeforeCancelReservation($dto->getNbHoursBeforeCancelReservation());
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
                $status = PlaceStatusEnum::from($dto->getStatus());
                $place->setStatus($status);
            } catch (\Exception) {
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
            'name'      => $this->placeDAL->findOneBy(['name' => $value]),
            'legalName' => $this->placeDAL->findOneBy(['legalName' => $value]),
            'siret'     => $this->placeDAL->findOneBy(['siret' => $value]),
            default     => throw new InvalidArgumentException(),
        };

        return $existingPlace === null || $existingPlace->getId() === $currentPlace->getId();
    }
}
