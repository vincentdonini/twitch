<?php

namespace App\Domain\Wod\Wod;

interface UpdateWodDTOInterface
{
    public function getId(): int;
    public function getName(): ?string;
    public function getTypeId(): ?int;
    public function getCategoryId(): ?int;
    public function getTeamSize(): ?int;
}
