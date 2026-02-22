<?php

namespace App\Domain\Wod\WodType;

use Symfony\Component\Uid\Uuid;

interface GetWodTypeByIdDTOInterface
{
    public function getId(): Uuid;
}
