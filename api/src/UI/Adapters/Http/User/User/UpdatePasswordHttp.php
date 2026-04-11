<?php

namespace App\UI\Adapters\Http\User\User;

use App\Domain\User\User\UpdatePasswordDTOInterface;

final readonly class UpdatePasswordHttp implements UpdatePasswordDTOInterface
{
    public function __construct(
        private string $currentPassword,
        private string $newPassword,
    ) {
    }

    public function getCurrentPassword(): string
    {
        return $this->currentPassword;
    }

    public function getNewPassword(): string
    {
        return $this->newPassword;
    }
}
