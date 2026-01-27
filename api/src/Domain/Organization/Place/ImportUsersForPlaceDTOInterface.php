<?php

namespace App\Domain\Organization\Place;

use Symfony\Component\Uid\Uuid;

interface ImportUsersForPlaceDTOInterface
{
    public function getId(): Uuid;
    public function getCsvPath(): string;
}
