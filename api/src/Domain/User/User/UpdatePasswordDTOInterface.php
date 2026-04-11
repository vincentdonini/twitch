<?php

namespace App\Domain\User\User;

interface UpdatePasswordDTOInterface
{
    public function getCurrentPassword(): string;
    public function getNewPassword(): string;
}
