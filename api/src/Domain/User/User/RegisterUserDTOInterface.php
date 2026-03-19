<?php

namespace App\Domain\User\User;

interface RegisterUserDTOInterface
{
    public function getEmail(): ?string;

    public function getPassword(): ?string;

    public function getFirstName(): ?string;

    public function getLastName(): ?string;
}
