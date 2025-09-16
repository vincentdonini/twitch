<?php

namespace App\Domain\Wod\Service;

use App\Domain\Wod\Entity\WodType;

final class WodTypeService
{
    public function __construct(
        private readonly WodTypeTranslatorInterface $wodTypeTranslator,
    ) {
    }

    public function enrichEntity(WodType $wodType): WodType
    {
        $wodType->setName($this->wodTypeTranslator->getName($wodType));
        $wodType->setDescription($this->wodTypeTranslator->getDescription($wodType));

        return $wodType;
    }
}
