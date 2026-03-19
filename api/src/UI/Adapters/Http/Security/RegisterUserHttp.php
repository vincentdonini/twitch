<?php

namespace App\UI\Adapters\Http\Security;

use App\Domain\User\User\RegisterUserDTOInterface;

final readonly class RegisterUserHttp implements RegisterUserDTOInterface
{
    public function __construct(
        private array $payload,
    ) {
    }

    public function getEmail(): ?string
    {
        return $this->payload['email'] ?? null;
    }

    public function getPassword(): ?string
    {
        return $this->payload['password'] ?? null;
    }

    public function getFirstName(): ?string
    {
        return $this->payload['firstName'] ?? null;
    }

    public function getLastName(): ?string
    {
        return $this->payload['lastName'] ?? null;
    }
}
