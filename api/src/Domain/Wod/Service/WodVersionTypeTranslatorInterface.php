<?php

namespace App\Domain\Wod\Service;

use App\Domain\Wod\Entity\WodVersionType;

interface WodVersionTypeTranslatorInterface
{
    public function getName(WodVersionType $wodVersionType): string;
    public function getDescription(WodVersionType $wodVersionType): string;
}
