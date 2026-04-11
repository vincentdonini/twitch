<?php

namespace App\Domain\Organization\Entity;

use App\Domain\Organization\Enum\FormulaBillingPeriodEnum;
use App\Domain\Organization\Enum\FormulaCurrencyEnum;
use App\Domain\Organization\Enum\FormulaStatusEnum;
use App\Domain\Organization\Enum\FormulaTypeEnum;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'formula')]
class Formula
{
    // -----------------------------------------------------------------------------------------------------------------
    // IDENTIFIERS
    // -----------------------------------------------------------------------------------------------------------------

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Place::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Place $place;

    #[ORM\Column(type: 'string', length: 150)]
    private string $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isPublic;

    #[ORM\Column(type: 'string', enumType: FormulaStatusEnum::class)]
    private FormulaStatusEnum $status;

    #[ORM\Column(type: 'integer')]
    private int $price;

    #[ORM\Column(type: 'string', enumType: FormulaCurrencyEnum::class)]
    private FormulaCurrencyEnum $currency;

    #[ORM\Column(type: 'string', enumType: FormulaTypeEnum::class)]
    private FormulaTypeEnum $type;

    #[ORM\Column(type: 'string', nullable: true, enumType: FormulaBillingPeriodEnum::class)]
    private ?FormulaBillingPeriodEnum $billingPeriod = null;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $engagementDurationInMonths = null;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $cancellationNoticeInDays = null;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $maxSessionsPerDay = null;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $maxSessionsPerWeek = null;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $maxSessionsPerMonth = null;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $totalSessions = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $validityInDays = null;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $minAge = null;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $maxAge = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(targetEntity: Subscription::class, mappedBy: 'formula')]
    private Collection $subscriptions;

    // -----------------------------------------------------------------------------------------------------------------
    // CONSTRUCTOR
    // -----------------------------------------------------------------------------------------------------------------

    private function __construct()
    {
        $this->id        = Uuid::v7();
        $this->createdAt = new DateTimeImmutable();
    }

    public static function createSubscription(
        Place                    $place,
        string                   $title,
        int                      $price,
        FormulaCurrencyEnum      $currency,
        FormulaStatusEnum        $status,
        bool                     $isPublic,
        FormulaBillingPeriodEnum $billingPeriod,
        int                      $engagementDurationInMonths,
        ?int                     $cancellationNoticeInDays = null,
        ?int                     $maxSessionsPerDay = null,
        ?int                     $maxSessionsPerWeek = null,
        ?int                     $maxSessionsPerMonth = null,
    ): self {
        $formula = new self();

        $formula->initializeCommon(
            $place,
            $title,
            $price,
            $currency,
            $status,
            $isPublic,
        );

        $formula->type                       = FormulaTypeEnum::SUBSCRIPTION;
        $formula->billingPeriod              = $billingPeriod;
        $formula->engagementDurationInMonths = $engagementDurationInMonths;
        $formula->cancellationNoticeInDays   = $cancellationNoticeInDays;
        $formula->maxSessionsPerDay          = $maxSessionsPerDay;
        $formula->maxSessionsPerWeek         = $maxSessionsPerWeek;
        $formula->maxSessionsPerMonth        = $maxSessionsPerMonth;
        $formula->totalSessions              = null;

        return $formula;
    }

    public static function createPack(
        Place               $place,
        string              $title,
        int                 $price,
        FormulaCurrencyEnum $currency,
        FormulaStatusEnum   $status,
        bool                $isPublic,
        int                 $totalSessions,
        ?int                $validityInDays = null,
        ?int                $maxSessionsPerDay = null,
        ?int                $maxSessionsPerWeek = null,
        ?int                $maxSessionsPerMonth = null,
    ): self {
        if ($totalSessions <= 1) {
            throw new DomainException('Pack must contain at least two sessions.');
        }

        self::assertMaxNotGreaterThanTotal($maxSessionsPerDay, $totalSessions, 'day');
        self::assertMaxNotGreaterThanTotal($maxSessionsPerWeek, $totalSessions, 'week');
        self::assertMaxNotGreaterThanTotal($maxSessionsPerMonth, $totalSessions, 'month');

        $formula = new self();

        $formula->initializeCommon(
            $place,
            $title,
            $price,
            $currency,
            $status,
            $isPublic,
        );

        $formula->type                = FormulaTypeEnum::PACK;
        $formula->totalSessions       = $totalSessions;
        $formula->validityInDays      = $validityInDays;
        $formula->maxSessionsPerDay   = $maxSessionsPerDay;
        $formula->maxSessionsPerWeek  = $maxSessionsPerWeek;
        $formula->maxSessionsPerMonth = $maxSessionsPerMonth;
        $formula->billingPeriod       = null;

        return $formula;
    }

    public static function createDropIn(
        Place               $place,
        string              $title,
        int                 $price,
        FormulaCurrencyEnum $currency,
        FormulaStatusEnum   $status,
        bool                $isPublic,
    ): self {
        $formula = new self();

        $formula->initializeCommon(
            $place,
            $title,
            $price,
            $currency,
            $status,
            $isPublic
        );

        $formula->type                = FormulaTypeEnum::DROP_IN;
        $formula->maxSessionsPerDay   = 1;
        $formula->maxSessionsPerWeek  = 1;
        $formula->maxSessionsPerMonth = 1;
        $formula->validityInDays      = 1;
        $formula->totalSessions       = 1;
        $formula->billingPeriod       = null;

        return $formula;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // GETTERS / SETTERS
    // -----------------------------------------------------------------------------------------------------------------

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getPlace(): Place
    {
        return $this->place;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): self
    {
        $this->isPublic = $isPublic;
        return $this;
    }

    public function getStatus(): FormulaStatusEnum
    {
        return $this->status;
    }

    public function setStatus(FormulaStatusEnum $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getCurrency(): FormulaCurrencyEnum
    {
        return $this->currency;
    }

    public function setCurrency(FormulaCurrencyEnum $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function getType(): FormulaTypeEnum
    {
        return $this->type;
    }

    public function setType(FormulaTypeEnum $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getBillingPeriod(): ?FormulaBillingPeriodEnum
    {
        return $this->billingPeriod;
    }

    public function setBillingPeriod(?FormulaBillingPeriodEnum $billingPeriod): self
    {
        $this->billingPeriod = $billingPeriod;
        return $this;
    }

    public function getEngagementDurationInMonths(): ?int
    {
        return $this->engagementDurationInMonths;
    }

    public function setEngagementDurationInMonths(?int $months): self
    {
        $this->engagementDurationInMonths = $months;
        return $this;
    }

    public function getCancellationNoticeInDays(): ?int
    {
        return $this->cancellationNoticeInDays;
    }

    public function setCancellationNoticeInDays(?int $days): self
    {
        $this->cancellationNoticeInDays = $days;
        return $this;
    }

    public function getMaxSessionsPerDay(): ?int
    {
        return $this->maxSessionsPerDay;
    }

    public function setMaxSessionsPerDay(?int $value): self
    {
        $this->maxSessionsPerDay = $value;
        return $this;
    }

    public function getMaxSessionsPerWeek(): ?int
    {
        return $this->maxSessionsPerWeek;
    }

    public function setMaxSessionsPerWeek(?int $value): self
    {
        $this->maxSessionsPerWeek = $value;
        return $this;
    }

    public function getMaxSessionsPerMonth(): ?int
    {
        return $this->maxSessionsPerMonth;
    }

    public function setMaxSessionsPerMonth(?int $value): self
    {
        $this->maxSessionsPerMonth = $value;
        return $this;
    }

    public function getTotalSessions(): ?int
    {
        return $this->totalSessions;
    }

    public function setTotalSessions(?int $totalSessions): self
    {
        $this->totalSessions = $totalSessions;
        return $this;
    }

    public function getValidityInDays(): ?int
    {
        return $this->validityInDays;
    }

    public function setValidityInDays(int $validityInDays): self
    {
        $this->validityInDays = $validityInDays;
        return $this;
    }

    public function getMinAge(): ?int
    {
        return $this->minAge;
    }

    public function setMinAge(?int $minAge): self
    {
        $this->minAge = $minAge;
        return $this;
    }

    public function getMaxAge(): ?int
    {
        return $this->maxAge;
    }

    public function setMaxAge(?int $maxAge): self
    {
        $this->maxAge = $maxAge;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $date): self
    {
        $this->updatedAt = $date;
        return $this;
    }

    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function hasSubscriptions(): bool
    {
        return !$this->subscriptions->isEmpty();
    }

    // -----------------------------------------------------------------------------------------------------------------

    private function initializeCommon(
        Place               $place,
        string              $title,
        int                 $price,
        FormulaCurrencyEnum $currency,
        FormulaStatusEnum   $status,
        bool                $isPublic
    ): void {
        $this->place    = $place;
        $this->title    = $title;
        $this->price    = $price;
        $this->currency = $currency;
        $this->status   = $status;
        $this->isPublic = $isPublic;
    }

    private static function assertMaxNotGreaterThanTotal(
        ?int   $max,
        int    $total,
        string $period
    ): void {
        if ($max !== null && $max > $total) {
            throw new DomainException(
                sprintf('Max sessions per %s cannot exceed total sessions.', $period)
            );
        }

        if ($max !== null && $max <= 0) {
            throw new DomainException(
                sprintf('Max sessions per %s must be greater than zero.', $period)
            );
        }
    }


    public function isSubscribable(): bool
    {
        if (
            !$this->isPublic() ||
            $this->getStatus() !== FormulaStatusEnum::ACTIVE
        ) {
           return false;
        }

        return true;
    }
}
