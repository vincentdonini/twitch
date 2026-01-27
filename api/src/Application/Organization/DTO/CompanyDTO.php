<?php

namespace App\Application\Organization\DTO;

use App\Domain\Geo\Entity\City;
use App\Domain\Geo\Entity\Country;
use App\Domain\Geo\Entity\Department;
use App\Domain\Geo\Entity\Region;
use App\Domain\Organization\Enum\CompanyStatusEnum;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class CompanyDTO
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
    public ?string $siren;

    #[Groups([
        FrontGroupsEnum::COMPANY_LIST_ADMIN, FrontGroupsEnum::COMPANY_DETAIL_ADMIN,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public ?string $vatNumber;

    #[Groups([
        FrontGroupsEnum::COMPANY_LIST_ADMIN, FrontGroupsEnum::COMPANY_DETAIL_ADMIN,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public ?string $legalForm;

    #[Groups([
        FrontGroupsEnum::COMPANY_LIST_ADMIN, FrontGroupsEnum::COMPANY_DETAIL_ADMIN,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public ?string $activityCode;

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
        FrontGroupsEnum::COMPANY_LIST_PUBLIC, FrontGroupsEnum::COMPANY_DETAIL_PUBLIC,
        FrontGroupsEnum::COMPANY_LIST_ADMIN, FrontGroupsEnum::COMPANY_DETAIL_ADMIN,
        FrontGroupsEnum::PLACE_LIST_PUBLIC, FrontGroupsEnum::PLACE_DETAIL_PUBLIC,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public CompanyStatusEnum $status;

    #[Groups([
        FrontGroupsEnum::COMPANY_LIST_PUBLIC, FrontGroupsEnum::COMPANY_DETAIL_PUBLIC,
        FrontGroupsEnum::COMPANY_LIST_ADMIN, FrontGroupsEnum::COMPANY_DETAIL_ADMIN,
        FrontGroupsEnum::PLACE_LIST_PUBLIC, FrontGroupsEnum::PLACE_DETAIL_PUBLIC,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public ?DateTimeImmutable $registrationDate;

    #[Groups([
        FrontGroupsEnum::COMPANY_LIST_PUBLIC, FrontGroupsEnum::COMPANY_DETAIL_PUBLIC,
        FrontGroupsEnum::COMPANY_LIST_ADMIN, FrontGroupsEnum::COMPANY_DETAIL_ADMIN,
        FrontGroupsEnum::PLACE_LIST_PUBLIC, FrontGroupsEnum::PLACE_DETAIL_PUBLIC,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public DateTimeImmutable $createdAt;

    #[Groups([
        FrontGroupsEnum::COMPANY_LIST_PUBLIC, FrontGroupsEnum::COMPANY_DETAIL_PUBLIC,
        FrontGroupsEnum::COMPANY_LIST_ADMIN, FrontGroupsEnum::COMPANY_DETAIL_ADMIN,
        FrontGroupsEnum::PLACE_LIST_PUBLIC, FrontGroupsEnum::PLACE_DETAIL_PUBLIC,
        FrontGroupsEnum::PLACE_LIST_ADMIN, FrontGroupsEnum::PLACE_DETAIL_ADMIN,
    ])]
    public ?DateTimeImmutable $updatedAt;

    public function __construct(
        Uuid               $id,
        string             $slug,
        string             $name,
        string             $legalName,
        ?string            $siren,
        ?string            $vatNumber,
        ?string            $legalForm,
        ?string            $activityCode,
        string             $address,
        ?string            $address2,
        string             $postalCode,
        City               $city,
        Department         $department,
        Region             $region,
        Country            $country,
        CompanyStatusEnum  $status,
        ?DateTimeImmutable $registrationDate,
        DateTimeImmutable  $createdAt,
        ?DateTimeImmutable $updatedAt,
    ) {
        $this->id               = $id;
        $this->slug             = $slug;
        $this->name             = $name;
        $this->legalName        = $legalName;
        $this->siren            = $siren;
        $this->vatNumber        = $vatNumber;
        $this->legalForm        = $legalForm;
        $this->activityCode     = $activityCode;
        $this->address          = $address;
        $this->address2         = $address2;
        $this->postalCode       = $postalCode;
        $this->city             = $city;
        $this->department       = $department;
        $this->region           = $region;
        $this->country          = $country;
        $this->status           = $status;
        $this->registrationDate = $registrationDate;
        $this->createdAt        = $createdAt;
        $this->updatedAt        = $updatedAt;
    }
}
