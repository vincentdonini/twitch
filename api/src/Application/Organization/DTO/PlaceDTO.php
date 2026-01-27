<?php

namespace App\Application\Organization\DTO;

use App\Domain\Geo\Entity\City;
use App\Domain\Geo\Entity\Country;
use App\Domain\Geo\Entity\Department;
use App\Domain\Geo\Entity\Region;
use App\Domain\Organization\Enum\PlaceStatusEnum;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class PlaceDTO
{
    #[Groups([
        FrontGroupsEnum::COMPANY_LIST_PUBLIC, FrontGroupsEnum::COMPANY_DETAIL_PUBLIC,
        FrontGroupsEnum::COMPANY_LIST_ADMIN, FrontGroupsEnum::COMPANY_DETAIL_ADMIN,
        FrontGroupsEnum::PLACE_LIST_PUBLIC, FrontGroupsEnum::PLACE_DETAIL_PUBLIC,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public Uuid $id;

    #[Groups([
        FrontGroupsEnum::COMPANY_LIST_PUBLIC, FrontGroupsEnum::COMPANY_DETAIL_PUBLIC,
        FrontGroupsEnum::COMPANY_LIST_ADMIN, FrontGroupsEnum::COMPANY_DETAIL_ADMIN,
        FrontGroupsEnum::PLACE_LIST_PUBLIC, FrontGroupsEnum::PLACE_DETAIL_PUBLIC,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public CompanyDTO $company;

    #[Groups([
        FrontGroupsEnum::COMPANY_LIST_PUBLIC, FrontGroupsEnum::COMPANY_DETAIL_PUBLIC,
        FrontGroupsEnum::COMPANY_LIST_ADMIN, FrontGroupsEnum::COMPANY_DETAIL_ADMIN,
        FrontGroupsEnum::PLACE_LIST_PUBLIC, FrontGroupsEnum::PLACE_DETAIL_PUBLIC,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public string $slug;

    #[Groups([
        FrontGroupsEnum::COMPANY_LIST_PUBLIC, FrontGroupsEnum::COMPANY_DETAIL_PUBLIC,
        FrontGroupsEnum::COMPANY_LIST_ADMIN, FrontGroupsEnum::COMPANY_DETAIL_ADMIN,
        FrontGroupsEnum::PLACE_LIST_PUBLIC, FrontGroupsEnum::PLACE_DETAIL_PUBLIC,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public string $name;

    #[Groups([
        FrontGroupsEnum::COMPANY_LIST_ADMIN, FrontGroupsEnum::COMPANY_DETAIL_ADMIN,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public string $legalName;

    #[Groups([
        FrontGroupsEnum::COMPANY_LIST_ADMIN, FrontGroupsEnum::COMPANY_DETAIL_ADMIN,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public ?string $siret;

    #[Groups([
        FrontGroupsEnum::COMPANY_LIST_PUBLIC, FrontGroupsEnum::COMPANY_DETAIL_PUBLIC,
        FrontGroupsEnum::COMPANY_LIST_ADMIN, FrontGroupsEnum::COMPANY_DETAIL_ADMIN,
        FrontGroupsEnum::PLACE_LIST_PUBLIC, FrontGroupsEnum::PLACE_DETAIL_PUBLIC,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public string $address;

    #[Groups([
        FrontGroupsEnum::COMPANY_LIST_PUBLIC, FrontGroupsEnum::COMPANY_DETAIL_PUBLIC,
        FrontGroupsEnum::COMPANY_LIST_ADMIN, FrontGroupsEnum::COMPANY_DETAIL_ADMIN,
        FrontGroupsEnum::PLACE_LIST_PUBLIC, FrontGroupsEnum::PLACE_DETAIL_PUBLIC,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public ?string $address2;

    #[Groups([
        FrontGroupsEnum::COMPANY_LIST_PUBLIC, FrontGroupsEnum::COMPANY_DETAIL_PUBLIC,
        FrontGroupsEnum::COMPANY_LIST_ADMIN, FrontGroupsEnum::COMPANY_DETAIL_ADMIN,
        FrontGroupsEnum::PLACE_LIST_PUBLIC, FrontGroupsEnum::PLACE_DETAIL_PUBLIC,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public string $postalCode;

    #[Groups([
        FrontGroupsEnum::COMPANY_LIST_PUBLIC, FrontGroupsEnum::COMPANY_DETAIL_PUBLIC,
        FrontGroupsEnum::COMPANY_LIST_ADMIN, FrontGroupsEnum::COMPANY_DETAIL_ADMIN,
        FrontGroupsEnum::PLACE_LIST_PUBLIC, FrontGroupsEnum::PLACE_DETAIL_PUBLIC,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public City $city;

    #[Groups([
        FrontGroupsEnum::COMPANY_LIST_PUBLIC, FrontGroupsEnum::COMPANY_DETAIL_PUBLIC,
        FrontGroupsEnum::COMPANY_LIST_ADMIN, FrontGroupsEnum::COMPANY_DETAIL_ADMIN,
        FrontGroupsEnum::PLACE_LIST_PUBLIC, FrontGroupsEnum::PLACE_DETAIL_PUBLIC,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public Department $department;

    #[Groups([
        FrontGroupsEnum::COMPANY_LIST_PUBLIC, FrontGroupsEnum::COMPANY_DETAIL_PUBLIC,
        FrontGroupsEnum::COMPANY_LIST_ADMIN, FrontGroupsEnum::COMPANY_DETAIL_ADMIN,
        FrontGroupsEnum::PLACE_LIST_PUBLIC, FrontGroupsEnum::PLACE_DETAIL_PUBLIC,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public Region $region;

    #[Groups([
        FrontGroupsEnum::COMPANY_LIST_PUBLIC, FrontGroupsEnum::COMPANY_DETAIL_PUBLIC,
        FrontGroupsEnum::COMPANY_LIST_ADMIN, FrontGroupsEnum::COMPANY_DETAIL_ADMIN,
        FrontGroupsEnum::PLACE_LIST_PUBLIC, FrontGroupsEnum::PLACE_DETAIL_PUBLIC,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public Country $country;

    #[Groups([
        FrontGroupsEnum::PLACE_LIST_PUBLIC, FrontGroupsEnum::PLACE_DETAIL_PUBLIC,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public PlaceStatusEnum $status;

    #[Groups([
        FrontGroupsEnum::PLACE_LIST_PUBLIC, FrontGroupsEnum::PLACE_DETAIL_PUBLIC,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public ?int $nbDaysBeforeReservation;

    #[Groups([
        FrontGroupsEnum::PLACE_LIST_PUBLIC, FrontGroupsEnum::PLACE_DETAIL_PUBLIC,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public ?int $nbHoursBeforeCancelReservation;

    #[Groups([
        FrontGroupsEnum::PLACE_LIST_PUBLIC, FrontGroupsEnum::PLACE_DETAIL_PUBLIC,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public ?string $phone;

    #[Groups([
        FrontGroupsEnum::PLACE_LIST_PUBLIC, FrontGroupsEnum::PLACE_DETAIL_PUBLIC,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public ?string $email;

    #[Groups([
        FrontGroupsEnum::PLACE_LIST_PUBLIC, FrontGroupsEnum::PLACE_DETAIL_PUBLIC,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public ?string $website;

    #[Groups([
        FrontGroupsEnum::PLACE_LIST_PUBLIC, FrontGroupsEnum::PLACE_DETAIL_PUBLIC,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public ?DateTimeImmutable $registrationDate;

    #[Groups([
        FrontGroupsEnum::PLACE_LIST_PUBLIC, FrontGroupsEnum::PLACE_DETAIL_PUBLIC,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public DateTimeImmutable $createdAt;

    #[Groups([
        FrontGroupsEnum::PLACE_LIST_PUBLIC, FrontGroupsEnum::PLACE_DETAIL_PUBLIC,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public ?DateTimeImmutable $updatedAt;

    public function __construct(
        Uuid               $id,
        CompanyDTO         $company,
        string             $slug,
        string             $name,
        string             $legalName,
        ?string            $siret,
        string             $address,
        ?string            $address2,
        string             $postalCode,
        City               $city,
        Department         $department,
        Region             $region,
        Country            $country,
        PlaceStatusEnum    $status,
        ?int               $nbDaysBeforeReservation,
        ?int               $nbHoursBeforeCancelReservation,
        ?string            $phone,
        ?string            $email,
        ?string            $website,
        ?DateTimeImmutable $registrationDate,
        DateTimeImmutable  $createdAt,
        ?DateTimeImmutable $updatedAt,
    ) {
        $this->id                             = $id;
        $this->company                        = $company;
        $this->slug                           = $slug;
        $this->name                           = $name;
        $this->legalName                      = $legalName;
        $this->siret                          = $siret;
        $this->address                        = $address;
        $this->address2                       = $address2;
        $this->postalCode                     = $postalCode;
        $this->city                           = $city;
        $this->department                     = $department;
        $this->region                         = $region;
        $this->country                        = $country;
        $this->status                         = $status;
        $this->nbDaysBeforeReservation        = $nbDaysBeforeReservation;
        $this->nbHoursBeforeCancelReservation = $nbHoursBeforeCancelReservation;
        $this->phone                          = $phone;
        $this->email                          = $email;
        $this->website                        = $website;
        $this->registrationDate               = $registrationDate;
        $this->createdAt                      = $createdAt;
        $this->updatedAt                      = $updatedAt;
    }
}
