<?php

namespace App\Domain\Organization\Subscription;

use App\Domain\Organization\Enum\SubscriptionStatusEnum;
use App\Domain\User\Entity\User;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

interface CancelSubscriptionDTOInterface
{
    public function getSubscriptionId(): Uuid;

    public function getAuthenticatedUserId(): Uuid;

    public function getCancelledRequestAt(): ?DateTimeImmutable;
}
