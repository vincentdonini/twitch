<?php

namespace App\UI\Adapters\Http\User\User;

use App\Domain\User\User\GetUserByIdDTOInterface;

class GetUserByIdHttp implements GetUserByIdDTOInterface
{
    public function __construct(
        private readonly string $id,
    ) {

    }

    public function getId(): string
    {
        return $this->id;
    }
}
