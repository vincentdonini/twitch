<?php

namespace App\Domain\Organization\Subscription;

use App\Domain\Core\Exceptions\EntityNotFoundException;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Organization\Entity\Subscription;
use App\Domain\Organization\Ports\SubscriptionDALInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\Ports\UserDALInterface;
use DomainException;

final readonly class CancelSubscriptionUseCase
{
    public function __construct(
        private DatabaseInterface        $database,
        private SubscriptionDALInterface $subscriptionDAL,
        private UserDALInterface         $userDAL,
    ) {
    }

    public function execute(
        CancelSubscriptionDTOInterface $dto
    ): void {
        $this->validatePayload($dto);

        $subscription = $this->subscriptionDAL->getById($dto->getSubscriptionId())
            ?? throw new EntityNotFoundException('Subscription not found.');

        $user = $this->userDAL->getById($dto->getAuthenticatedUserId())
            ?? throw new EntityNotFoundException('User not found.');

        if (!$subscription->getUser()->getId()->equals($user->getId())) {
            throw new DomainException('User not allowed to cancel this subscription.');
        }

        $subscription->requestCancellation(
            $dto->getCancelledRequestAt() ?? new \DateTimeImmutable()
        );

        $this->database->preSave($subscription);
        $this->database->save();
    }

    private function validatePayload(CancelSubscriptionDTOInterface $dto): void
    {
        if (
            $dto->getSubscriptionId() === null ||
            $dto->getAuthenticatedUserId() === null
        ) {
            throw new InvalidPayloadException('Missing required identifiers.');
        }
    }
}