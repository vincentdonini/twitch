<?php

namespace App\Domain\Organization\Place;

interface CreatePlaceDTOInterface
{
    public function getCompanyId(): ?string;
    public function getSlug(): ?string;
    public function getName(): ?string;
    public function getLegalName(): ?string;
    public function getSiret(): ?string;
    public function getAddress(): ?string;
    public function getAddress2(): ?string;
    public function getPostalCode(): ?string;
    public function getCity(): ?string;
    public function getCountry(): ?string;
    public function getTimezone(): ?string;
    public function getNbDaysBeforeReservation(): ?string;
    public function getNbHoursBeforeCancelReservation(): ?string;
    public function getLat(): ?string;
    public function getLng(): ?string;
    public function getPhone(): ?string;
    public function getEmail(): ?string;
    public function getWebsite(): ?string;
    public function getRegistrationDate(): ?string;
    public function getStatus(): ?string;
}
