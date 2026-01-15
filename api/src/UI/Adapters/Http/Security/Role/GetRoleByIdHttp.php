<?php

namespace App\UI\Adapters\Http\Security\Role;


use App\Domain\Security\Role\GetRoleByIdDTOInterface;

class GetRoleByIdHttp implements GetRoleByIdDTOInterface
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
