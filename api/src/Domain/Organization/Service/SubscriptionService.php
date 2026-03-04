<?php

namespace App\Domain\Organization\Service;

use App\Application\Common\CollectionMapper;
use App\Application\Organization\DTO\SubscriptionDTO;
use App\Domain\Organization\Entity\Subscription;
use App\Domain\User\Service\UserService;
use App\Infrastructure\Filters\FilterCollection;

final readonly class SubscriptionService
{
    public function __construct(
        private UserService    $userService,
        private PlaceService   $placeService,
        private FormulaService $formulaService,
    ) {
    }

    public function transformToDTO(Subscription $subscription, FilterCollection $filters = null): SubscriptionDTO
    {
        $userDTO    = $this->userService->transformToDTO($subscription->getUser(), $filters);
        $placeDTO   = $this->placeService->transformToDTO($subscription->getPlace(), $filters);
        $formulaDTO = $this->formulaService->transformToDTO($subscription->getFormula(), $filters);

        return new SubscriptionDTO(
            id                         : $subscription->getId(),
            user                       : $userDTO,
            place                      : $placeDTO,
            formula                    : $formulaDTO,
            status                     : $subscription->getStatus(),
            startedAt                  : $subscription->getStartedAt(),
            endedAt                    : $subscription->getEndedAt(),
            nextBillingAt              : $subscription->getNextBillingAt(),
            cancelRequestedAt          : $subscription->getCancelRequestedAt(),
            price                      : $subscription->getPrice(),
            currency                   : $subscription->getCurrency(),
            sessionsUsedInCurrentPeriod: $subscription->getSessionsUsedInCurrentPeriod(),
            createdAt                  : $subscription->getCreatedAt(),
            updatedAt                  : $subscription->getUpdatedAt(),
        );
    }

    public function transformCollectionToDTO(array $subscriptions, FilterCollection $filters = null): array
    {
        return CollectionMapper::mapAndFilter(
            $subscriptions,
            fn(Subscription $subscription) => $this->transformToDTO($subscription, $filters)
        );
    }
}
