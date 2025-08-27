<?php

namespace App\Domain\Wod\Service;

use App\Application\Wod\DTO\WodCategoryDTO;
use App\Domain\Wod\Entity\WodCategory;

final class WodCategoryService
{
    public function __construct(
        private readonly WodCategoryTranslatorInterface $wodCategoryTranslator,
    ) {

    }

    public function transformToDTO(WodCategory $wodCategory): WodCategoryDTO
    {
        return new WodCategoryDTO(
            $wodCategory->getId(),
            $wodCategory->getSlug(),
            $this->wodCategoryTranslator->getName($wodCategory),
            $this->wodCategoryTranslator->getDescription($wodCategory),
        );
    }
}
