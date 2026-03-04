<?php

namespace App\Application\Organization\DTO;

use App\Domain\Organization\Enum\FormulaBillingPeriodEnum;
use App\Domain\Organization\Enum\FormulaCurrencyEnum;
use App\Domain\Organization\Enum\FormulaStatusEnum;
use App\Domain\Organization\Enum\FormulaTypeEnum;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class FormulaDTO
{
    #[Groups([
        FrontGroupsEnum::FORMULA_LIST, FrontGroupsEnum::FORMULA_DETAIL,
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public Uuid $id;

    #[Groups([
        FrontGroupsEnum::FORMULA_LIST, FrontGroupsEnum::FORMULA_DETAIL,
    ])]
    public PlaceDTO $place;

    #[Groups([
        FrontGroupsEnum::FORMULA_LIST, FrontGroupsEnum::FORMULA_DETAIL,
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public string $title;

    #[Groups([
        FrontGroupsEnum::FORMULA_LIST, FrontGroupsEnum::FORMULA_DETAIL,
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public ?string $description;

    #[Groups([
        FrontGroupsEnum::FORMULA_LIST, FrontGroupsEnum::FORMULA_DETAIL,
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public bool $isPublic;

    #[Groups([
        FrontGroupsEnum::FORMULA_LIST, FrontGroupsEnum::FORMULA_DETAIL,
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public FormulaStatusEnum $status;

    #[Groups([
        FrontGroupsEnum::FORMULA_LIST, FrontGroupsEnum::FORMULA_DETAIL,
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public int $price;

    #[Groups([
        FrontGroupsEnum::FORMULA_LIST, FrontGroupsEnum::FORMULA_DETAIL,
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public FormulaCurrencyEnum $currency;

    #[Groups([
        FrontGroupsEnum::FORMULA_LIST, FrontGroupsEnum::FORMULA_DETAIL,
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public FormulaTypeEnum $type;

    #[Groups([
        FrontGroupsEnum::FORMULA_LIST, FrontGroupsEnum::FORMULA_DETAIL,
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public ?FormulaBillingPeriodEnum $billingPeriod;

    #[Groups([
        FrontGroupsEnum::FORMULA_LIST, FrontGroupsEnum::FORMULA_DETAIL,
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public ?int $engagementDurationInMonths;

    #[Groups([
        FrontGroupsEnum::FORMULA_LIST, FrontGroupsEnum::FORMULA_DETAIL,
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public ?int $cancellationNoticeInDays;

    #[Groups([
        FrontGroupsEnum::FORMULA_LIST, FrontGroupsEnum::FORMULA_DETAIL,
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public ?int $maxSessionsPerDay;

    #[Groups([
        FrontGroupsEnum::FORMULA_LIST, FrontGroupsEnum::FORMULA_DETAIL,
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public ?int $maxSessionsPerWeek;

    #[Groups([
        FrontGroupsEnum::FORMULA_LIST, FrontGroupsEnum::FORMULA_DETAIL,
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public ?int $maxSessionsPerMonth;

    #[Groups([
        FrontGroupsEnum::FORMULA_LIST, FrontGroupsEnum::FORMULA_DETAIL,
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public ?int $totalSessions;

    #[Groups([
        FrontGroupsEnum::FORMULA_LIST, FrontGroupsEnum::FORMULA_DETAIL,
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public ?int $validityInDays;

    #[Groups([
        FrontGroupsEnum::FORMULA_LIST, FrontGroupsEnum::FORMULA_DETAIL,
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public ?int $minAge;

    #[Groups([
        FrontGroupsEnum::FORMULA_LIST, FrontGroupsEnum::FORMULA_DETAIL,
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public ?int $maxAge;

    #[Groups([
        FrontGroupsEnum::FORMULA_LIST, FrontGroupsEnum::FORMULA_DETAIL,
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public DateTimeImmutable $createdAt;

    #[Groups([
        FrontGroupsEnum::FORMULA_LIST, FrontGroupsEnum::FORMULA_DETAIL,
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public ?DateTimeImmutable $updatedAt;

    public function __construct(
        Uuid                     $id,
        PlaceDTO                 $place,
        string                   $title,
        ?string                  $description,
        bool                     $isPublic,
        FormulaStatusEnum        $status,
        int                      $price,
        FormulaCurrencyEnum      $currency,
        FormulaTypeEnum          $type,
        ?FormulaBillingPeriodEnum $billingPeriod,
        ?int                     $engagementDurationInMonths,
        ?int                     $cancellationNoticeInDays,
        ?int                     $maxSessionsPerDay,
        ?int                     $maxSessionsPerWeek,
        ?int                     $maxSessionsPerMonth,
        ?int                     $totalSessions,
        ?int                     $validityInDays,
        ?int                     $minAge,
        ?int                     $maxAge,
        DateTimeImmutable        $createdAt,
        ?DateTimeImmutable       $updatedAt,
    ) {
        $this->id                         = $id;
        $this->place                      = $place;
        $this->title                      = $title;
        $this->description                = $description;
        $this->isPublic                   = $isPublic;
        $this->status                     = $status;
        $this->price                      = $price;
        $this->currency                   = $currency;
        $this->type                       = $type;
        $this->billingPeriod              = $billingPeriod;
        $this->engagementDurationInMonths = $engagementDurationInMonths;
        $this->cancellationNoticeInDays   = $cancellationNoticeInDays;
        $this->totalSessions              = $totalSessions;
        $this->validityInDays             = $validityInDays;
        $this->maxSessionsPerDay          = $maxSessionsPerDay;
        $this->maxSessionsPerWeek         = $maxSessionsPerWeek;
        $this->maxSessionsPerMonth        = $maxSessionsPerMonth;
        $this->minAge                     = $minAge;
        $this->maxAge                     = $maxAge;
        $this->createdAt                  = $createdAt;
        $this->updatedAt                  = $updatedAt;
    }
}
