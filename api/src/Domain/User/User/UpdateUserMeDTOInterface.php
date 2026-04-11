<?php

namespace App\Domain\User\User;

interface UpdateUserMeDTOInterface
{
    public function getEmail(): ?string;
    public function getFirstName(): ?string;
    public function getLastName(): ?string;
}
