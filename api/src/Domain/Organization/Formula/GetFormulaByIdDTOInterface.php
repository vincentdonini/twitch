<?php

namespace App\Domain\Organization\Formula;


use Symfony\Component\Uid\Uuid;

interface GetFormulaByIdDTOInterface
{
    public function getId(): Uuid;
}
