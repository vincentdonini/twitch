<?php

namespace App\UI\Adapters\Http\Organization\Subscription;

use App\Domain\Organization\Enum\SubscriptionStatusEnum;
use App\Domain\Organization\Subscription\UpdateSubscriptionDTOInterface;
use App\UI\Adapters\Http\Common\HttpPayloadParser;
use Symfony\Component\Uid\Uuid;

final readonly class UpdateSubscriptionHttp implements UpdateSubscriptionDTOInterface
{
    use HttpPayloadParser;

    public function __construct(
        private Uuid  $placeId,
        private Uuid  $formulaId,
        private Uuid  $subscriptionId,
        private Uuid  $userId,
        private array $payload,
    ) {
    }

    public function getSubscriptionId(): Uuid
    {
        return $this->subscriptionId;
    }

    public function getPlaceId(): Uuid
    {
        return $this->placeId;
    }

    public function getFormulaId(): Uuid
    {
        return $this->formulaId;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getStatus(): ?SubscriptionStatusEnum
    {
        /** @var SubscriptionStatusEnum|null */
        return $this->parseEnum('status', SubscriptionStatusEnum::class);
    }
}
