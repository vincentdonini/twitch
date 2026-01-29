<?php

namespace App\UI\Adapters\Http\Organization\Place;

use App\Domain\Organization\Place\UpdatePlaceDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UpdatePlaceHttp implements UpdatePlaceDTOInterface
{
    public function __construct(
        private Uuid  $id,
        private array $payload,
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCompanyId(): ?string
    {
        return $this->payload['companyId'] ?? null;
    }

    public function getName(): ?string
    {
        return $this->payload['name'] ?? null;
    }

    public function getLegalName(): ?string
    {
        return $this->payload['legalName'] ?? null;
    }

    public function getSiret(): ?string
    {
        return $this->payload['siret'] ?? null;
    }

    public function getAddress(): ?string
    {
        return $this->payload['address'] ?? null;
    }

    public function getAddress2(): ?string
    {
        return $this->payload['address2'] ?? null;
    }

    public function getPostalCode(): ?string
    {
        return $this->payload['postalCode'] ?? null;
    }

    public function getCity(): ?string
    {
        return $this->payload['city'] ?? null;
    }

    public function getNbDaysBeforeReservation(): ?string
    {
        return $this->payload['nbDaysBeforeReservation'] ?? null;
    }

    public function getNbHoursBeforeCancelReservation(): ?string
    {
        return $this->payload['nbHoursBeforeCancelReservation'] ?? null;
    }

    public function getPhone(): ?string
    {
        return $this->payload['phone'] ?? null;
    }

    public function getEmail(): ?string
    {
        return $this->payload['email'] ?? null;
    }

    public function getWebsite(): ?string
    {
        return $this->payload['website'] ?? null;
    }

    public function getRegistrationDate(): ?string
    {
        return $this->payload['registrationDate'] ?? null;
    }

    public function getStatus(): ?string
    {
        return $this->payload['status'] ?? null;
    }
}
