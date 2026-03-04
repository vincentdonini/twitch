<?php

namespace App\Domain\Organization\Entity;

use App\Domain\Organization\Enum\FormulaBillingPeriodEnum;
use App\Domain\Organization\Enum\FormulaCurrencyEnum;
use App\Domain\User\Entity\User;
use App\Domain\Organization\Enum\SubscriptionStatusEnum;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'subscription')]
class Subscription
{
    // -----------------------------------------------------------------------------------------------------------------
    // IDENTIFIERS
    // -----------------------------------------------------------------------------------------------------------------

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Place::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Place $place;

    #[ORM\ManyToOne(targetEntity: Formula::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Formula $formula;

    // -----------------------------------------------------------------------------------------------------------------
    // STATUS
    // -----------------------------------------------------------------------------------------------------------------

    #[ORM\Column(type: 'string', enumType: SubscriptionStatusEnum::class)]
    private SubscriptionStatusEnum $status;

    // -----------------------------------------------------------------------------------------------------------------
    // DATES
    // -----------------------------------------------------------------------------------------------------------------

    #[ORM\Column(type: 'date_immutable')]
    private DateTimeImmutable $startedAt;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?DateTimeImmutable $endedAt = null;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?DateTimeImmutable $nextBillingAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $cancelRequestedAt = null;

    // -----------------------------------------------------------------------------------------------------------------
    // SNAPSHOT FINANCIER (important pour l’historique)
    // -----------------------------------------------------------------------------------------------------------------

    #[ORM\Column(type: 'integer')]
    private int $price;

    #[ORM\Column(type: 'string', enumType: FormulaCurrencyEnum::class)]
    private FormulaCurrencyEnum $currency;

    // -----------------------------------------------------------------------------------------------------------------
    // TRACKING UTILISATION
    // -----------------------------------------------------------------------------------------------------------------

    #[ORM\Column(type: 'integer')]
    private int $sessionsUsedInCurrentPeriod = 0;

    // -----------------------------------------------------------------------------------------------------------------
    // METADATA
    // -----------------------------------------------------------------------------------------------------------------

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    // -----------------------------------------------------------------------------------------------------------------
    // CONSTRUCTOR
    // -----------------------------------------------------------------------------------------------------------------

    private function __construct()
    {
        $this->id        = Uuid::v7();
        $this->createdAt = new DateTimeImmutable();
    }

    // -----------------------------------------------------------------------------------------------------------------
    // GETTERS / SETTERS
    // -----------------------------------------------------------------------------------------------------------------

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPlace(): Place
    {
        return $this->place;
    }

    public function getFormula(): Formula
    {
        return $this->formula;
    }

    public function getStatus(): SubscriptionStatusEnum
    {
        return $this->status;
    }

    public function getStartedAt(): DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function getEndedAt(): ?DateTimeImmutable
    {
        return $this->endedAt;
    }

    public function getNextBillingAt(): ?DateTimeImmutable
    {
        return $this->nextBillingAt;
    }

    public function setNextBillingAt(?DateTimeImmutable $date): self
    {
        $this->nextBillingAt = $date;
        return $this;
    }

    public function getCancelRequestedAt(): ?DateTimeImmutable
    {
        return $this->cancelRequestedAt;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getCurrency(): FormulaCurrencyEnum
    {
        return $this->currency;
    }

    public function getSessionsUsedInCurrentPeriod(): int
    {
        return $this->sessionsUsedInCurrentPeriod;
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

    // -----------------------------------------------------------------------------------------------------------------
    // BUSINESS METHODS
    // -----------------------------------------------------------------------------------------------------------------

    public static function create(
        User                   $user,
        Place                  $place,
        Formula                $formula,
        SubscriptionStatusEnum $status,
        ?DateTimeImmutable     $startedAt = null,
    ): self {
        if (!$formula->isSubscribable()) {
            throw new DomainException('Formula is not subscribable.');
        }

        $now          = new DateTimeImmutable();
        $startingDate = $startedAt ?? $now;

        $subscription = new self();

        $subscription->id      = Uuid::v7();
        $subscription->user    = $user;
        $subscription->place   = $place;
        $subscription->formula = $formula;

        $subscription->status    = $status;
        $subscription->startedAt = $startingDate;
        $subscription->endedAt   = self::calculateEndedAt(
            $subscription->startedAt,
            $formula->getEngagementDurationInMonths()
        );

        $subscription->nextBillingAt = self::calculateNextBillingAt(
            $subscription->startedAt,
            $formula->getBillingPeriod()
        );

        $subscription->price    = $formula->getPrice();
        $subscription->currency = $formula->getCurrency();

        $subscription->createdAt = $now;

        return $subscription;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            SubscriptionStatusEnum::ACTIVE,
            SubscriptionStatusEnum::PENDING,
        ], true);
    }

    public function requestCancellation(\DateTimeImmutable $requestedAt): void
    {
        if (!$this->canBeCancelled()) {
            throw new DomainException('Subscription cannot be cancelled.');
        }

        $engagementEndDate = $this->getEngagementEndDate();
        if ($requestedAt < $engagementEndDate) {
            throw new DomainException('Engagement period not finished.');
        }

        $this->cancelRequestedAt = $requestedAt;
        $this->endedAt           = $this->calculateEffectiveCancellationDate($requestedAt);

        $this->status = SubscriptionStatusEnum::CANCELLED;
    }

    private function getEngagementEndDate(): \DateTimeImmutable
    {
        $engagementMonths = $this->formula->getEngagementDurationInMonths();
        return $engagementMonths
            ? $this->startedAt->modify("+{$engagementMonths} months")
            : $this->startedAt;
    }

    private function calculateEffectiveCancellationDate(\DateTimeImmutable $requestedAt): \DateTimeImmutable
    {
        $noticeDays = $this->formula->getCancellationNoticeInDays();

        if ($noticeDays === null || $noticeDays <= 0) {
            return $requestedAt;
        }

        return $requestedAt->modify("+{$noticeDays} days");
    }

    public function cancel(?DateTimeImmutable $date = null): self
    {
        if (!$this->canBeCancelled()) {
            throw new DomainException('Invalid state transition.');
        }

        $this->status            = SubscriptionStatusEnum::CANCELLED;
        $this->cancelRequestedAt = $date ?? new DateTimeImmutable();
        $this->endedAt           = $date;
        return $this;
    }


    public function suspend(): self
    {
        if (!$this->canBeCancelled()) {
            throw new DomainException('Invalid state transition.');
        }

        $this->status = SubscriptionStatusEnum::SUSPENDED;
        return $this;
    }

    public function expire(DateTimeImmutable $date): self
    {
        $this->status  = SubscriptionStatusEnum::EXPIRED;
        $this->endedAt = $date;
        return $this;
    }

    public function renew(DateTimeImmutable $nextBillingAt): self
    {
        $this->status                      = SubscriptionStatusEnum::ACTIVE;
        $this->nextBillingAt               = $nextBillingAt;
        $this->sessionsUsedInCurrentPeriod = 0;
        return $this;
    }

    public function incrementSessions(): self
    {
        $this->sessionsUsedInCurrentPeriod++;
        return $this;
    }

    public function resetUsage(): self
    {
        $this->sessionsUsedInCurrentPeriod = 0;
        return $this;
    }

    private static function calculateEndedAt(
        DateTimeImmutable $startedAt,
        ?int              $engagementDurationInMonths
    ): DateTimeImmutable {
        if ($engagementDurationInMonths === null) {
            return $startedAt;
        }

        return $startedAt->modify('+' . $engagementDurationInMonths . ' months');
    }

    private static function calculateNextBillingAt(
        DateTimeImmutable         $startedAt,
        ?FormulaBillingPeriodEnum $billingPeriod
    ): ?DateTimeImmutable {
        if ($billingPeriod === null) {
            return null;
        }

        return match ($billingPeriod) {
            FormulaBillingPeriodEnum::WEEKLY  => $startedAt->modify('+1 week'),
            FormulaBillingPeriodEnum::MONTHLY => $startedAt->modify('+1 month'),
            FormulaBillingPeriodEnum::YEARLY  => $startedAt->modify('+1 year'),
        };
    }
}