<?php

namespace App\Domain\Organization\Subscription;

use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

interface CancelSubscriptionDTOInterface
{
    public function getSubscriptionId(): Uuid;

    public function getAuthenticatedUserId(): Uuid;

    public function getCancelledRequestAt(): ?DateTimeImmutable;
}
