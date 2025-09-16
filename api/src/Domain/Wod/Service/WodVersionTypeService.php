<?php

namespace App\Domain\Wod\Service;

use App\Domain\Wod\Entity\WodVersionType;

final class WodVersionTypeService
{
    public function __construct(
        private readonly WodVersionTypeTranslatorInterface $wodVersionTypeTranslator,
    )
    {
    }

    public function enrichEntity(WodVersionType $wodVersionType): WodVersionType
    {
        $wodVersionType->setName($this->wodVersionTypeTranslator->getName($wodVersionType));
        $wodVersionType->setDescription($this->wodVersionTypeTranslator->getDescription($wodVersionType));

        return $wodVersionType;
    }
}
