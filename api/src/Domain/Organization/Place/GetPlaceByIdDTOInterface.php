<?php

namespace App\Domain\Organization\Place;


use Symfony\Component\Uid\Uuid;

interface GetPlaceByIdDTOInterface
{
    public function getId(): Uuid;
}
