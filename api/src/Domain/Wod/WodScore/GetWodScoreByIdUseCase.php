<?php

namespace App\Domain\Wod\WodScore;

use App\Domain\Wod\Entity\WodScore;
use App\Domain\Wod\Ports\WodScoreDALInterface;
use Doctrine\ORM\EntityNotFoundException;

final readonly class GetWodScoreByIdUseCase
{
    public function __construct(
        private WodScoreDALInterface $wodScoreDAL,
    ) {
    }

    public function execute(GetWodScoreByIdDTOInterface $dto): WodScore
    {
        $wodScore = $this->wodScoreDAL->getById($dto->getId());
        if (!$wodScore instanceof WodScore) {
            throw new EntityNotFoundException();
        }

        return $wodScore;
    }
}

