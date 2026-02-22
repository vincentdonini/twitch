<?php

namespace App\Domain\Wod\Wod;

use Symfony\Component\Uid\Uuid;

interface UpdateWodDTOInterface
{
    public function getId(): Uuid;

    public function getName(): ?string;

    public function getTypeId(): ?Uuid;

    public function getCategoryId(): ?Uuid;

    public function getTeamSize(): ?int;
}
