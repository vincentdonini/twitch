<?php

namespace App\Domain\Organization\Subscription;

use App\Domain\Organization\Entity\Subscription;
use App\Domain\Organization\Ports\SubscriptionDALInterface;
use App\Domain\Core\Exceptions\EntityNotFoundException;

readonly class GetSubscriptionByIdUseCase
{
    public function __construct(
        private SubscriptionDALInterface $subscriptionDAL,
    ) {
    }

    public function execute(GetSubscriptionByIdDTOInterface $dto): Subscription
    {
        $subscription = $this->subscriptionDAL->getById($dto->getId());
        if (!$subscription instanceof Subscription) {
            throw new EntityNotFoundException('Subscription not found.');
        }

        if ($dto->getPlaceId() !== null && !$subscription->getPlace()->getId()->equals($dto->getPlaceId())) {
            throw new EntityNotFoundException('Subscription does not belong to this place.');
        }

        if ($dto->getFormulaId() !== null && !$subscription->getFormula()->getId()->equals($dto->getFormulaId())) {
            throw new EntityNotFoundException('Subscription does not belong to this formula.');
        }

        return $subscription;
    }
}