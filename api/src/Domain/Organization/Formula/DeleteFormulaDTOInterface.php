<?php

namespace App\Domain\Organization\Formula;

use Symfony\Component\Uid\Uuid;

interface DeleteFormulaDTOInterface
{
    public function getId(): Uuid;

    public function getPlaceId(): ?Uuid;
}
