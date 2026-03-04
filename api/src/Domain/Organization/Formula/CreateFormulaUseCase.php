<?php

namespace App\Domain\Organization\Formula;

use App\Domain\Core\Exceptions\AlreadyExistException;
use App\Domain\Core\Exceptions\EntityNotFoundException;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Organization\Entity\Formula;
use App\Domain\Organization\Entity\Place;
use App\Domain\Organization\Enum\FormulaTypeEnum;
use App\Domain\Organization\Ports\FormulaDALInterface;
use App\Domain\Organization\Ports\PlaceDALInterface;
use InvalidArgumentException;

final readonly class CreateFormulaUseCase
{
    public function __construct(
        private DatabaseInterface   $database,
        private PlaceDALInterface   $placeDAL,
        private FormulaDALInterface $formulaDAL,
    ) {
    }

    public function execute(CreateFormulaDTOInterface $dto): Formula
    {
        if (!$this->validatePayload($dto)) {
            throw new InvalidPayloadException();
        }

        $place = $this->placeDAL->getById($dto->getPlaceId());
        if (!$place instanceof Place) {
            throw new EntityNotFoundException();
        }

        if (!$this->validateDuplicate('title', $dto->getTitle(), $place)) {
            throw new AlreadyExistException();
        }

        $formula = match ($dto->getType()) {
            FormulaTypeEnum::SUBSCRIPTION => Formula::createSubscription(
                place                     : $place,
                title                     : $dto->getTitle(),
                price                     : $dto->getPrice(),
                currency                  : $dto->getCurrency(),
                status                    : $dto->getStatus(),
                isPublic                  : $dto->isPublic(),
                billingPeriod             : $dto->getBillingPeriod(),
                engagementDurationInMonths: $dto->getEngagementDurationInMonths(),
                cancellationNoticeInDays  : $dto->getCancellationNoticeInDays(),
                maxSessionsPerDay         : $dto->getMaxSessionsPerDay(),
                maxSessionsPerWeek        : $dto->getMaxSessionsPerWeek(),
                maxSessionsPerMonth       : $dto->getMaxSessionsPerMonth(),
            ),

            FormulaTypeEnum::PACK         => Formula::createPack(
                place              : $place,
                title              : $dto->getTitle(),
                price              : $dto->getPrice(),
                currency           : $dto->getCurrency(),
                status             : $dto->getStatus(),
                isPublic           : $dto->isPublic(),
                totalSessions      : $dto->getTotalSessions(),
                validityInDays     : $dto->getValidityInDays(),
                maxSessionsPerDay  : $dto->getMaxSessionsPerDay(),
                maxSessionsPerWeek : $dto->getMaxSessionsPerWeek(),
                maxSessionsPerMonth: $dto->getMaxSessionsPerMonth(),
            ),

            FormulaTypeEnum::DROP_IN      => Formula::createDropIn(
                place   : $place,
                title   : $dto->getTitle(),
                price   : $dto->getPrice(),
                currency: $dto->getCurrency(),
                status  : $dto->getStatus(),
                isPublic: $dto->isPublic(),
            ),
        };

        $formula->setMinAge($dto->getMinAge());
        $formula->setMaxAge($dto->getMaxAge());

        $this->database->preSave($formula);
        $this->database->save();

        return $formula;
    }

    private function validatePayload(CreateFormulaDTOInterface $dto): bool
    {
        if (
            $dto->getPlaceId() === null ||
            $dto->getTitle() === null ||
            $dto->getPrice() === null ||
            $dto->getCurrency() === null ||
            $dto->getStatus() === null ||
            $dto->getType() === null
        ) {
            return false;
        }

        return match ($dto->getType()) {
            FormulaTypeEnum::SUBSCRIPTION => $this->validateSubscriptionPayload($dto),
            FormulaTypeEnum::PACK         => $this->validatePackPayload($dto),
            FormulaTypeEnum::DROP_IN      => $this->validateDropInPayload($dto),
        };
    }

    private function validateSubscriptionPayload(CreateFormulaDTOInterface $dto): bool
    {
        if ($dto->getBillingPeriod() === null) {
            return false;
        }

        if ($dto->getEngagementDurationInMonths() === null) {
            return false;
        }

        return true;
    }

    private function validatePackPayload(CreateFormulaDTOInterface $dto): bool
    {
        if ($dto->getTotalSessions() === null || $dto->getTotalSessions() <= 1) {
            return false;
        }

        return true;
    }

    private function validateDropInPayload(CreateFormulaDTOInterface $dto): bool
    {
        if ($dto->getBillingPeriod() !== null) {
            return false;
        }

        if ($dto->getTotalSessions() !== null) {
            return false;
        }

        return true;
    }

    private function validateDuplicate(string $field, string $value, Place $place): bool
    {
        $existingFormula = match ($field) {
            'title' => $this->formulaDAL->findOneBy(['title' => $value, 'place' => $place]),
            default => throw new InvalidArgumentException(),
        };

        return $existingFormula === null;
    }
}
