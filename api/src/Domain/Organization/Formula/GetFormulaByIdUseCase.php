<?php

namespace App\Domain\Organization\Formula;

use App\Domain\Organization\Entity\Formula;
use App\Domain\Organization\Ports\FormulaDALInterface;
use Doctrine\ORM\EntityNotFoundException;

readonly class GetFormulaByIdUseCase
{
    public function __construct(
        private FormulaDALInterface $formulaDAL,
    ) {

    }

    public function execute(GetFormulaByIdDTOInterface $dto): Formula
    {
        $formula = $this->formulaDAL->getById($dto->getId());
        if (!$formula instanceof Formula) {
            throw new EntityNotFoundException();
        }

        return $formula;
    }
}
