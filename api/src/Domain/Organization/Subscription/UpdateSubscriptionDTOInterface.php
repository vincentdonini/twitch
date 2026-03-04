<?php

namespace App\Domain\Organization\Subscription;

use App\Domain\Organization\Enum\SubscriptionStatusEnum;
use Symfony\Component\Uid\Uuid;

interface UpdateSubscriptionDTOInterface
{
    public function getSubscriptionId(): ?Uuid;

    public function getPlaceId(): ?Uuid;

    public function getFormulaId(): ?Uuid;

    public function getStatus(): ?SubscriptionStatusEnum;
}
