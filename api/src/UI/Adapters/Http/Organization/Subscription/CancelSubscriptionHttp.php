<?php

namespace App\UI\Adapters\Http\Organization\Subscription;

use App\Domain\Organization\Enum\SubscriptionStatusEnum;
use App\Domain\Organization\Subscription\CancelSubscriptionDTOInterface;
use App\Domain\Organization\Subscription\CreateSubscriptionDTOInterface;
use App\UI\Adapters\Http\Common\HttpPayloadParser;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

final readonly class CancelSubscriptionHttp implements CancelSubscriptionDTOInterface
{
    use HttpPayloadParser;

    public function __construct(
        private Uuid  $subscriptionId,
        private Uuid  $authenticatedUserId,
        private array $payload,
    ) {
    }

    public function getSubscriptionId(): Uuid
    {
        return $this->subscriptionId;
    }

    public function getAuthenticatedUserId(): Uuid
    {
        return $this->authenticatedUserId;
    }

    public function getCancelledRequestAt(): ?DateTimeImmutable
    {
        return $this->parseDateTimeImmutable('cancelled_request_at', 'Y-m-d');
    }
}
