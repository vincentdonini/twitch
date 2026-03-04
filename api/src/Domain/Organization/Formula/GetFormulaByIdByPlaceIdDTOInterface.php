<?php

namespace App\Domain\Organization\Formula;


use Symfony\Component\Uid\Uuid;

interface GetFormulaByIdByPlaceIdDTOInterface
{
    public function getId(): Uuid;

    public function getPlaceId(): Uuid;
}
