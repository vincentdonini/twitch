<?php

namespace App\Domain\Organization\Formula;

use App\Domain\Core\Exceptions\EntityNotFoundException;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Organization\Entity\Formula;
use App\Domain\Organization\Enum\FormulaTypeEnum;
use App\Domain\Organization\Ports\FormulaDALInterface;
use App\Domain\Organization\Ports\PlaceDALInterface;
use DomainException;

final readonly class UpdateFormulaUseCase
{
    public function __construct(
        private DatabaseInterface $database,
        private PlaceDALInterface $placeDAL,
        private FormulaDALInterface $formulaDAL,
    ) {}

    public function execute(UpdateFormulaDTOInterface $dto): Formula
    {
        $formula = $this->formulaDAL->getById($dto->getId())
            ?? throw new EntityNotFoundException();

        $place = $this->placeDAL->getById($dto->getPlaceId())
            ?? throw new EntityNotFoundException();

        if (!$formula->getPlace()->getId()->equals($place->getId())) {
            throw new InvalidPayloadException();
        }

        if ($formula->hasSubscriptions()) {
            throw new DomainException();
        }

        $this->updateFromDto($formula, $dto);

        $this->database->preSave($formula);
        $this->database->save();

        return $formula;
    }

    public function updateFromDto(Formula $formula, UpdateFormulaDTOInterface $dto): void
    {
        match ($formula->getType()) {
            FormulaTypeEnum::SUBSCRIPTION => $this->updateSubscription($formula, $dto),
            FormulaTypeEnum::PACK         => $this->updatePack($formula, $dto),
            FormulaTypeEnum::DROP_IN      => $this->updateDropIn($formula, $dto),
        };
    }

    private function updateCommonFields(Formula $formula, UpdateFormulaDTOInterface $dto): void
    {
        if ($dto->getDescription() !== null) {
            $formula->setDescription($dto->getDescription());
        }
        if ($dto->isPublic() !== null) {
            $formula->setIsPublic($dto->isPublic());
        }
        if ($dto->getMaxSessionsPerDay() !== null) {
            $formula->setMaxSessionsPerDay($dto->getMaxSessionsPerDay());
        }
        if ($dto->getMaxSessionsPerWeek() !== null) {
            $formula->setMaxSessionsPerWeek($dto->getMaxSessionsPerWeek());
        }
        if ($dto->getMaxSessionsPerMonth() !== null) {
            $formula->setMaxSessionsPerMonth($dto->getMaxSessionsPerMonth());
        }
        if ($dto->getMinAge() !== null) {
            $formula->setMinAge($dto->getMinAge());
        }
        if ($dto->getMaxAge() !== null) {
            $formula->setMaxAge($dto->getMaxAge());
        }
        if ($dto->getPrice() !== null) {
            $formula->setPrice($dto->getPrice());
        }
        if ($dto->getCurrency() !== null) {
            $formula->setCurrency($dto->getCurrency());
        }
        if ($dto->getStatus() !== null) {
            $formula->setStatus($dto->getStatus());
        }
        if ($dto->getTitle() !== null) {
            $formula->setTitle($dto->getTitle());
        }
    }

    private function updateSubscription(Formula $formula, UpdateFormulaDTOInterface $dto): void
    {
        if ($dto->getBillingPeriod() !== null) {
            $formula->setBillingPeriod($dto->getBillingPeriod());
        }
        if ($dto->getEngagementDurationInMonths() !== null) {
            $formula->setEngagementDurationInMonths($dto->getEngagementDurationInMonths());
        }
        if ($dto->getCancellationNoticeInDays() !== null) {
            $formula->setCancellationNoticeInDays($dto->getCancellationNoticeInDays());
        }

        $this->updateCommonFields($formula, $dto);
    }

    private function updatePack(Formula $formula, UpdateFormulaDTOInterface $dto): void
    {
        if ($dto->getTotalSessions() !== null) {
            $formula->setTotalSessions($dto->getTotalSessions());
        }
        if ($dto->getValidityInDays() !== null) {
            $formula->setValidityInDays($dto->getValidityInDays());
        }

        $this->updateCommonFields($formula, $dto);
    }

    private function updateDropIn(Formula $formula, UpdateFormulaDTOInterface $dto): void
    {
        $this->updateCommonFields($formula, $dto);
    }
}