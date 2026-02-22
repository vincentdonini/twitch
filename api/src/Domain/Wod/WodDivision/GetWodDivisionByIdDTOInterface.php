<?php

namespace App\Domain\Wod\WodDivision;

use Symfony\Component\Uid\Uuid;

interface GetWodDivisionByIdDTOInterface
{
    public function getId(): Uuid;
}
