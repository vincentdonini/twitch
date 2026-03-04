<?php

namespace App\Domain\Organization\Subscription;

use App\Domain\Core\Exceptions\AlreadyExistException;
use App\Domain\Core\Exceptions\EntityNotFoundException;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Organization\Entity\Formula;
use App\Domain\Organization\Entity\Subscription;
use App\Domain\Organization\Ports\FormulaDALInterface;
use App\Domain\Organization\Ports\PlaceDALInterface;
use App\Domain\Organization\Ports\SubscriptionDALInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\Ports\UserDALInterface;
use DateTimeImmutable;
use DomainException;

final readonly class CreateSubscriptionUseCase
{
    public function __construct(
        private DatabaseInterface        $database,
        private PlaceDALInterface        $placeDAL,
        private FormulaDALInterface      $formulaDAL,
        private UserDALInterface         $userDAL,
        private SubscriptionDALInterface $subscriptionDAL,
    ) {
    }

    public function execute(
        CreateSubscriptionDTOInterface $dto,
    ): Subscription {

        $this->validatePayload($dto);

        $place = $this->placeDAL->getById($dto->getPlaceId())
            ?? throw new EntityNotFoundException('Place not found.');

        $formula = $this->formulaDAL->getById($dto->getFormulaId())
            ?? throw new EntityNotFoundException('Formula not found.');

        $user = $this->userDAL->getById($dto->getUserId())
            ?? throw new EntityNotFoundException('User not found.');

        if (!$formula->getPlace()->getId()->equals($place->getId())) {
            throw new DomainException('Formula does not belong to this place.');
        }

        if (!$formula->isSubscribable()) {
            throw new InvalidPayloadException('Formula not subscribable.');
        }

        $this->checkUserAgeEligibility($user, $formula);
        $this->checkExistingUserFormula($user, $formula);

        $subscription = Subscription::create(
            $user,
            $place,
            $formula,
            $dto->getStatus(),
            $dto->getStartedAt(),
        );

        $this->database->preSave($subscription);
        $this->database->save();

        return $subscription;
    }

    private function validatePayload(CreateSubscriptionDTOInterface $dto): void
    {
        if (
            $dto->getPlaceId() === null ||
            $dto->getFormulaId() === null ||
            $dto->getUserId() === null ||
            $dto->getStatus() === null
        ) {
            throw new InvalidPayloadException('Missing required identifiers.');
        }
    }

    private function checkExistingUserFormula(User $user, Formula $formula): void
    {
        $existing = $this->subscriptionDAL
            ->findExistingForUserAndFormula($user, $formula);

        if ($existing) {
            throw new AlreadyExistException('Active subscription already exists.');
        }
    }

    private function checkUserAgeEligibility(User $user, Formula $formula): void
    {
        $minAge = $formula->getMinAge();
        $maxAge = $formula->getMaxAge();

        if ($minAge === null && $maxAge === null) {
            return;
        }

        $birthDate = $user->getBirthDate();

        if ($birthDate === null) {
            throw new DomainException('User birth date is required for this formula.');
        }

        $age = $birthDate->diff(new DateTimeImmutable())->y;

        if ($minAge !== null && $age < $minAge) {
            throw new DomainException('User too young for this formula.');
        }

        if ($maxAge !== null && $age > $maxAge) {
            throw new DomainException('User too old for this formula.');
        }
    }
}