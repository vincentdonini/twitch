<?php

namespace App\Domain\Wod\WodAgeRange;

use Symfony\Component\Uid\Uuid;

interface GetWodAgeRangeByIdDTOInterface
{
    public function getId(): Uuid;
}
