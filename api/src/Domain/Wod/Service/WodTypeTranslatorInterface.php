<?php

namespace App\Domain\Wod\Service;

use App\Domain\Wod\Entity\WodType;

interface WodTypeTranslatorInterface
{
    public function getName(WodType $wodType): string;
    public function getDescription(WodType $wodType): string;
}
