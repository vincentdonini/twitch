<?php

namespace App\Domain\Organization\Subscription;

use App\Domain\Core\Exceptions\EntityNotFoundException;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Organization\Entity\Subscription;
use App\Domain\Organization\Enum\SubscriptionStatusEnum;
use App\Domain\Organization\Ports\SubscriptionDALInterface;
use DateTimeImmutable;
use DomainException;

final readonly class UpdateSubscriptionUseCase
{
    public function __construct(
        private DatabaseInterface        $database,
        private SubscriptionDALInterface $subscriptionDAL,
    ) {
    }

    public function execute(UpdateSubscriptionDTOInterface $dto): Subscription
    {
        $this->validatePayload($dto);

        $subscription = $this->subscriptionDAL->getById($dto->getSubscriptionId())
            ?? throw new EntityNotFoundException('Subscription not found.');

        if (!$subscription->getPlace()->getId()->equals($dto->getPlaceId())) {
            throw new EntityNotFoundException('Subscription does not belong to this place.');
        }

        if (!$subscription->getFormula()->getId()->equals($dto->getFormulaId())) {
            throw new EntityNotFoundException('Subscription does not belong to this formula.');
        }

        $this->applyStatusChange($subscription, $dto->getStatus());

        $subscription->setUpdatedAt(new DateTimeImmutable());

        $this->database->preSave($subscription);
        $this->database->save();

        return $subscription;
    }

    private function validatePayload(UpdateSubscriptionDTOInterface $dto): void
    {
        if (
            $dto->getSubscriptionId() === null ||
            $dto->getPlaceId() === null ||
            $dto->getFormulaId() === null
        ) {
            throw new InvalidPayloadException('Missing required identifiers.');
        }

        if ($dto->getStatus() === null) {
            throw new InvalidPayloadException('At least one field must be provided for update.');
        }
    }

    private function applyStatusChange(Subscription $subscription, SubscriptionStatusEnum $status): void
    {
        $now = new DateTimeImmutable();

        match ($status) {
            SubscriptionStatusEnum::CANCELLED => $subscription->cancel($now),
            SubscriptionStatusEnum::SUSPENDED => $subscription->suspend(),
            SubscriptionStatusEnum::EXPIRED   => $subscription->expire($now),
            SubscriptionStatusEnum::ACTIVE    => $subscription->renew($now->modify('+1 month')),
            default                           => throw new DomainException(
                sprintf('Status "%s" cannot be set directly.', $status->value)
            ),
        };
    }
}
