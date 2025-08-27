<?php

namespace App\Domain\Wod\Service;

use App\Domain\Wod\Entity\WodCategory;

interface WodCategoryTranslatorInterface
{
    public function getName(WodCategory $wodCategory): string;
    public function getDescription(WodCategory $wodCategory): string;
}
