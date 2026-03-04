<?php

namespace App\Domain\Organization\Formula;

use App\Domain\Organization\Entity\Formula;
use App\Domain\Organization\Entity\Place;
use App\Domain\Organization\Ports\FormulaDALInterface;
use App\Domain\Organization\Ports\PlaceDALInterface;
use Doctrine\ORM\EntityNotFoundException;
use InvalidArgumentException;

readonly class GetFormulaByIdByPlaceIdUseCase
{
    public function __construct(
        private FormulaDALInterface $formulaDAL,
        private PlaceDALInterface   $placeDAL,
    ) {
    }

    public function execute(GetFormulaByIdByPlaceIdDTOInterface $dto): Formula
    {
        $formula = $this->formulaDAL->getById($dto->getId());
        if (!$formula instanceof Formula) {
            throw new EntityNotFoundException();
        }

        $place = $this->placeDAL->getById($dto->getPlaceId());
        if (!$place instanceof Place) {
            throw new EntityNotFoundException();
        }

        if (!$formula->getPlace()->getId()->equals($place->getId())) {
            throw new InvalidArgumentException();
        }

        return $formula;
    }
}
