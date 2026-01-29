<?php

namespace App\Domain\Organization\Company;

use Symfony\Component\Uid\Uuid;

interface UpdateCompanyDTOInterface
{
    public function getId(): Uuid;
    public function getName(): ?string;
    public function getLegalName(): ?string;
    public function getSiren(): ?string;
    public function getVatNumber(): ?string;
    public function getLegalForm(): ?string;
    public function getActivityCode(): ?string;
    public function getAddress(): ?string;
    public function getAddress2(): ?string;
    public function getPostalCode(): ?string;
    public function getCity(): ?string;
    public function getPhone(): ?string;
    public function getEmail(): ?string;
    public function getRegistrationDate(): ?string;
    public function getStatus(): ?string;
}
