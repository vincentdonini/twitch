<?php

namespace App\Domain\Organization\Formula;

use App\Domain\Core\Exceptions\EntityNotFoundException;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Organization\Entity\Formula;
use App\Domain\Organization\Ports\FormulaDALInterface;
use App\Domain\Organization\Ports\PlaceDALInterface;
use DomainException;

final readonly class DeleteFormulaUseCase
{
    public function __construct(
        private DatabaseInterface   $database,
        private PlaceDALInterface   $placeDAL,
        private FormulaDALInterface $formulaDAL,
    ) {
    }

    public function execute(DeleteFormulaDTOInterface $dto): void
    {
        $formula = $this->formulaDAL->getById($dto->getId());
        if (!$formula instanceof Formula) {
            throw new EntityNotFoundException();
        }

        $place = $this->placeDAL->getById($dto->getPlaceId());
        if (!$place) {
            throw new InvalidPayloadException();
        }

        if (!$formula->getPlace()->getId()->equals($place->getId())) {
            throw new InvalidPayloadException();
        }

        if ($this->formulaDAL->hasSubscriptions($formula)) {
            throw new DomainException();
        }

        $this->database->remove($formula);
        $this->database->save();
    }
}
