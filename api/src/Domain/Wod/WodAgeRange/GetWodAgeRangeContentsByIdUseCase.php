<?php

namespace App\Domain\Wod\WodAgeRange;

use App\Domain\Wod\Entity\WodAgeRange;
use App\Domain\Wod\Ports\WodAgeRangeDALInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityNotFoundException;

final readonly class GetWodAgeRangeContentsByIdUseCase
{
    public function __construct(
        private WodAgeRangeDALInterface $wodAgeRangeDAL,
    ) {
    }

    public function execute(GetWodAgeRangeByIdDTOInterface $dto): Collection
    {
        $wodAgeRange = $this->wodAgeRangeDAL->getById($dto->getId());
        if (!$wodAgeRange instanceof WodAgeRange) {
            throw new EntityNotFoundException();
        }

        return $wodAgeRange->getContents();
    }
}

