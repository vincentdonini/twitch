<?php

namespace App\Domain\Organization\Subscription;

use App\Domain\Organization\Enum\SubscriptionStatusEnum;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

interface CreateSubscriptionDTOInterface
{
    public function getPlaceId(): Uuid;

    public function getFormulaId(): Uuid;

    public function getUserId(): Uuid;

    public function getStatus(): SubscriptionStatusEnum;

    public function getStartedAt(): ?DateTimeImmutable;
}
