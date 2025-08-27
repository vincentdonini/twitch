<?php

namespace App\Domain\Wod\Service;

use App\Application\Wod\DTO\WodVersionTypeDTO;
use App\Domain\Wod\Entity\WodVersionType;

final class WodVersionTypeService
{
    public function __construct(
        private readonly WodVersionTypeTranslatorInterface $wodVersionTypeTranslator,
    )
    {

    }

    public function transformToDTO(WodVersionType $wodVersionType): WodVersionTypeDTO
    {
        return new WodVersionTypeDTO(
            $wodVersionType->getId(),
            $wodVersionType->getSlug(),
            $this->wodVersionTypeTranslator->getName($wodVersionType),
            $this->wodVersionTypeTranslator->getDescription($wodVersionType)
        );
    }
}
