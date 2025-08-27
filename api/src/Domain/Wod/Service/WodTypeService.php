<?php

namespace App\Domain\Wod\Service;

use App\Application\Wod\DTO\WodTypeDTO;
use App\Domain\Wod\Entity\WodType;

final class WodTypeService
{
    public function __construct(
        private readonly WodTypeTranslatorInterface $wodTypeTranslator,
    ) {
    }

    public function transformToDTO(WodType $wodType): WodTypeDTO
    {
        return new WodTypeDTO(
            $wodType->getId(),
            $wodType->getSlug(),
            $this->wodTypeTranslator->getName($wodType),
            $this->wodTypeTranslator->getDescription($wodType)
        );
    }
}
