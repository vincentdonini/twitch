<?php

namespace App\UI\Adapters\Http\Organization\Company;

use App\Domain\Organization\Company\UpdateCompanyDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UpdateCompanyHttp implements UpdateCompanyDTOInterface
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

    public function getSlug(): ?string
    {
        return $this->payload['slug'] ?? null;
    }

    public function getName(): ?string
    {
        return $this->payload['name'] ?? null;
    }

    public function getLegalName(): ?string
    {
        return $this->payload['legalName'] ?? null;
    }

    public function getSiren(): ?string
    {
        return $this->payload['siren'] ?? null;
    }

    public function getVatNumber(): ?string
    {
        return $this->payload['vatNumber'] ?? null;
    }

    public function getLegalForm(): ?string
    {
        return $this->payload['legalForm'] ?? null;
    }

    public function getActivityCode(): ?string
    {
        return $this->payload['activityCode'] ?? null;
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

    public function getCountry(): ?string
    {
        return $this->payload['country'] ?? null;
    }

    public function getTimezone(): ?string
    {
        return $this->payload['timezone'] ?? null;
    }

    public function getPhone(): ?string
    {
        return $this->payload['phone'] ?? null;
    }

    public function getEmail(): ?string
    {
        return $this->payload['email'] ?? null;
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
