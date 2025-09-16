<?php

namespace App\Domain\Movement\Service;

use App\Domain\Movement\Entity\MovementCategory;
use App\Infrastructure\Translator\Movement\MovementCategoryTranslator;

final class MovementCategoryService
{
    public function __construct(
        private readonly MovementCategoryTranslator $movementCategoryTranslator
    ) {
    }

    public function enrichEntity(MovementCategory $movementCategory): MovementCategory
    {
        $movementCategory->setName($this->movementCategoryTranslator->getName($movementCategory));
        $movementCategory->setDescription($this->movementCategoryTranslator->getDescription($movementCategory));

        return $movementCategory;
    }
}
