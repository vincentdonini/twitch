<?php

namespace App\Application\Organization\DTO;

use App\Application\User\DTO\UserDTO;
use App\Domain\Organization\Enum\FormulaCurrencyEnum;
use App\Domain\Organization\Enum\SubscriptionStatusEnum;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class SubscriptionDTO
{
    #[Groups([
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public Uuid $id;

    #[Groups([
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public UserDTO $user;

    #[Groups([
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public PlaceDTO $place;

    #[Groups([
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public FormulaDTO $formula;

    #[Groups([
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public SubscriptionStatusEnum $status;

    #[Groups([
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public DateTimeImmutable $startedAt;

    #[Groups([
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public ?DateTimeImmutable $endedAt;

    #[Groups([
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public ?DateTimeImmutable $nextBillingAt;

    #[Groups([
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public ?DateTimeImmutable $cancelRequestedAt;

    #[Groups([
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public int $price;

    #[Groups([
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public FormulaCurrencyEnum $currency;

    #[Groups([
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public int $sessionsUsedInCurrentPeriod;

    #[Groups([
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public DateTimeImmutable $createdAt;

    #[Groups([
        FrontGroupsEnum::SUBSCRIPTION_LIST, FrontGroupsEnum::SUBSCRIPTION_DETAIL,
    ])]
    public ?DateTimeImmutable $updatedAt;

    public function __construct(
        Uuid                   $id,
        UserDTO                $user,
        PlaceDTO               $place,
        FormulaDTO             $formula,
        SubscriptionStatusEnum $status,
        DateTimeImmutable      $startedAt,
        ?DateTimeImmutable     $endedAt,
        ?DateTimeImmutable     $nextBillingAt,
        ?DateTimeImmutable     $cancelRequestedAt,
        int                    $price,
        FormulaCurrencyEnum    $currency,
        int                    $sessionsUsedInCurrentPeriod,
        DateTimeImmutable      $createdAt,
        ?DateTimeImmutable     $updatedAt,
    ) {
        $this->id                          = $id;
        $this->user                        = $user;
        $this->place                       = $place;
        $this->formula                     = $formula;
        $this->status                      = $status;
        $this->startedAt                   = $startedAt;
        $this->endedAt                     = $endedAt;
        $this->nextBillingAt               = $nextBillingAt;
        $this->cancelRequestedAt           = $cancelRequestedAt;
        $this->price                       = $price;
        $this->currency                    = $currency;
        $this->sessionsUsedInCurrentPeriod = $sessionsUsedInCurrentPeriod;
        $this->createdAt                   = $createdAt;
        $this->updatedAt                   = $updatedAt;
    }
}
