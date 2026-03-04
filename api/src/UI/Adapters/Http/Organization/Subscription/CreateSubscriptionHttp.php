<?php

namespace App\UI\Adapters\Http\Organization\Subscription;

use App\Domain\Organization\Enum\SubscriptionStatusEnum;
use App\Domain\Organization\Subscription\CreateSubscriptionDTOInterface;
use App\UI\Adapters\Http\Common\HttpPayloadParser;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

final readonly class CreateSubscriptionHttp implements CreateSubscriptionDTOInterface
{
    use HttpPayloadParser;

    public function __construct(
        private Uuid                   $placeId,
        private Uuid                   $formulaId,
        private Uuid                   $userId,
        private SubscriptionStatusEnum $status,
        private array                  $payload,
    ) {
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

    public function getStatus(): SubscriptionStatusEnum
    {
        return $this->status;
    }

    public function getStartedAt(): ?DateTimeImmutable
    {
        return $this->parseDateTimeImmutable('started_at', 'Y-m-d');
    }
}
