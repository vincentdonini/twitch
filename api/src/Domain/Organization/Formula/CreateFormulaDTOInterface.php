<?php

namespace App\Domain\Organization\Formula;

use App\Domain\Organization\Enum\FormulaBillingPeriodEnum;
use App\Domain\Organization\Enum\FormulaCurrencyEnum;
use App\Domain\Organization\Enum\FormulaStatusEnum;
use App\Domain\Organization\Enum\FormulaTypeEnum;
use Symfony\Component\Uid\Uuid;

interface CreateFormulaDTOInterface
{
    public function getPlaceId(): ?Uuid;

    public function getTitle(): ?string;

    public function getDescription(): ?string;

    public function isPublic(): ?bool;

    public function getStatus(): ?FormulaStatusEnum;

    public function getPrice(): ?int;

    public function getCurrency(): ?FormulaCurrencyEnum;

    public function getType(): ?FormulaTypeEnum;

    public function getBillingPeriod(): ?FormulaBillingPeriodEnum;

    public function getEngagementDurationInMonths(): ?int;

    public function getCancellationNoticeInDays(): ?int;

    public function getMaxSessionsPerDay(): ?int;

    public function getMaxSessionsPerWeek(): ?int;

    public function getMaxSessionsPerMonth(): ?int;

    public function getTotalSessions(): ?int;

    public function getValidityInDays(): ?int;

    public function getMinAge(): ?int;

    public function getMaxAge(): ?string;
}
