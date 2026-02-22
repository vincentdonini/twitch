<?php

namespace App\Domain\Wod\Wod;

use Symfony\Component\Uid\Uuid;

interface GetWodByIdDTOInterface
{
    public function getId(): Uuid;
}
