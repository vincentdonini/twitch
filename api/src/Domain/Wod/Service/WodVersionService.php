<?php

namespace App\Domain\Wod\Service;

use App\Domain\Wod\Entity\WodVersion;

final class WodVersionService
{
    public function __construct(
        private readonly WodTypeTranslatorInterface $wodTypeTranslator,
    ) {
    }

    public function enrichEntity(WodVersion $wodVersion): WodVersion
    {
//        $wodVersion->setName($this->wodVersionTranslator->getName($wodVersion));
//        $wodVersion->setDescription($this->wodTypeTranslator->getDescription($wodVersion));

        return $wodVersion;
    }
}
