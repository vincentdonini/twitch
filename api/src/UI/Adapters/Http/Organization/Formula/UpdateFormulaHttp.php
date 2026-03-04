<?php

namespace App\UI\Adapters\Http\Organization\Formula;

use App\Domain\Organization\Enum\FormulaBillingPeriodEnum;
use App\Domain\Organization\Enum\FormulaCurrencyEnum;
use App\Domain\Organization\Enum\FormulaStatusEnum;
use App\Domain\Organization\Formula\UpdateFormulaDTOInterface;
use App\UI\Adapters\Http\Common\HttpPayloadParser;
use Symfony\Component\Uid\Uuid;

final readonly class UpdateFormulaHttp implements UpdateFormulaDTOInterface
{
    use HttpPayloadParser;

    public function __construct(
        private Uuid  $id,
        private Uuid  $placeId,
        private array $payload,
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getPlaceId(): Uuid
    {
        return $this->placeId;
    }

    public function getTitle(): ?string
    {
        return $this->payload['title'] ?? null;
    }

    public function getDescription(): ?string
    {
        return $this->payload['description'] ?? null;
    }

    public function isPublic(): ?bool
    {
        return $this->parseBoolean('isPublic');
    }

    public function getStatus(): ?FormulaStatusEnum
    {
        /** @var FormulaStatusEnum|null */
        return $this->parseEnum('status', FormulaStatusEnum::class);
    }

    public function getPrice(): ?int
    {
        return $this->payload['price'] ?? null;
    }

    public function getCurrency(): ?FormulaCurrencyEnum
    {
        /** @var FormulaCurrencyEnum|null */
        return $this->parseEnum('currency', FormulaCurrencyEnum::class);
    }

    public function getBillingPeriod(): ?FormulaBillingPeriodEnum
    {
        /** @var FormulaBillingPeriodEnum|null */
        return $this->parseEnum('billingPeriod', FormulaBillingPeriodEnum::class);
    }

    public function getEngagementDurationInMonths(): ?int
    {
        return $this->payload['engagementDurationInMonths'] ?? null;
    }

    public function getCancellationNoticeInDays(): ?int
    {
        return $this->payload['cancellationNoticeInDays'] ?? null;
    }

    public function getMaxSessionsPerDay(): ?int
    {
        return $this->payload['maxSessionsPerDay'] ?? null;
    }

    public function getMaxSessionsPerWeek(): ?int
    {
        return $this->payload['maxSessionsPerWeek'] ?? null;
    }

    public function getMaxSessionsPerMonth(): ?int
    {
        return $this->payload['maxSessionsPerMonth'] ?? null;
    }

    public function getTotalSessions(): ?int
    {
        return $this->payload['totalSessions'] ?? null;
    }

    public function getValidityInDays(): ?int
    {
        return $this->payload['validityInDays'] ?? null;
    }

    public function getMinAge(): ?int
    {
        return $this->payload['minAge'] ?? null;
    }

    public function getMaxAge(): ?string
    {
        return $this->payload['maxAge'] ?? null;
    }
}
