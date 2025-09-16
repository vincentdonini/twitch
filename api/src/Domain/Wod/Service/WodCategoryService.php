<?php

namespace App\Domain\Wod\Service;

use App\Domain\Wod\Entity\WodCategory;

final class WodCategoryService
{
    public function __construct(
        private readonly WodCategoryTranslatorInterface $wodCategoryTranslator,
    ) {
    }

    public function enrichEntity(WodCategory $wodCategory): WodCategory
    {
        $wodCategory->setName($this->wodCategoryTranslator->getName($wodCategory));
        $wodCategory->setDescription($this->wodCategoryTranslator->getDescription($wodCategory));

        return $wodCategory;
    }
}
