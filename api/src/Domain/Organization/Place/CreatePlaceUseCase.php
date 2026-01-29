<?php

namespace App\Domain\Organization\Place;

use App\Domain\Core\Exceptions\AlreadyExistException;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Geo\Ports\CityDALInterface;
use App\Domain\Organization\Entity\Place;
use App\Domain\Organization\Enum\PlaceStatusEnum;
use App\Domain\Organization\Ports\CompanyDALInterface;
use App\Domain\Organization\Ports\PlaceDALInterface;
use App\Shared\Utils\StringHelper;
use InvalidArgumentException;

final readonly class CreatePlaceUseCase
{
    public function __construct(
        private DatabaseInterface   $database,
        private PlaceDALInterface   $placeDAL,
        private CompanyDALInterface $companyDAL,
        private CityDALInterface    $cityDAL,
    ) {
    }

    public function execute(CreatePlaceDTOInterface $dto): Place
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

        if ($dto->getSiret() !== null && !$this->validateDuplicate('siret', $dto->getSiret())) {
            throw new AlreadyExistException();
        }

        $company = $this->companyDAL->getById($dto->getCompanyId());
        if (!$company) {
            throw new InvalidPayloadException();
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

        $place = new Place(
            company    : $company,
            slug       : StringHelper::slugify($dto->getName()),
            name       : $dto->getName(),
            legalName  : $dto->getLegalName(),
            address    : $dto->getAddress(),
            postalCode : $dto->getPostalCode(),
            city       : $city,
        );

        $place->setSiret($dto->getSiret() ?? null);
        $place->setAddress2($dto->getAddress2() ?? null);
        $place->setNbDaysBeforeReservation($dto->getNbDaysBeforeReservation() ?? null);
        $place->setNbHoursBeforeCancelReservation($dto->getNbHoursBeforeCancelReservation() ?? null);
        $place->setPhone($dto->getPhone() ?? null);
        $place->setEmail($dto->getEmail() ?? null);
        $place->setWebsite($dto->getWebsite() ?? null);
        $place->setRegistrationDate($dto->getRegistrationDate() ?? null);

        try {
            $status = PlaceStatusEnum::from($dto->getStatus());
            $place->setStatus($status);
        } catch (\Exception) {
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
            !$dto->getCity()
        ) {
            return false;
        }
        return true;
    }

    private function validateDuplicate(string $field, string $value): bool
    {
        $existingCompany = match ($field) {
            'name'      => $this->placeDAL->findOneBy(['name' => $value]),
            'legalName' => $this->placeDAL->findOneBy(['legalName' => $value]),
            'siret'     => $this->placeDAL->findOneBy(['siret' => $value]),
            default     => throw new InvalidArgumentException(),
        };

        return $existingCompany === null;
    }
}
