<?php

namespace App\Domain\Movement\Service;

use App\Domain\Movement\Entity\Movement;

interface MovementTranslatorInterface
{
    public function getName(Movement $movement): string;
    public function getDescription(Movement $movement): string;
}
