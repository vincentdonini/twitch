<?php

namespace App\Domain\Wod\WodScore;

use Symfony\Component\Uid\Uuid;

interface GetWodScoreByIdDTOInterface
{
    public function getId(): Uuid;
}
