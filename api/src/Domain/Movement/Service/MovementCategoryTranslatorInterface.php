<?php

namespace App\Domain\Movement\Service;

use App\Domain\Movement\Entity\MovementCategory;

interface MovementCategoryTranslatorInterface
{
    public function getName(MovementCategory $movementCategory): string;
    public function getDescription(MovementCategory $movementCategory): string;
}
