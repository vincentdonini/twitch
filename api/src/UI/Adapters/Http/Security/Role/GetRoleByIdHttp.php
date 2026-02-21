<?php

namespace App\UI\Adapters\Http\Security\Role;


use App\Domain\Security\Role\GetRoleByIdDTOInterface;

final readonly class GetRoleByIdHttp implements GetRoleByIdDTOInterface
{
    public function __construct(
        private string $id,
    ) {

    }

    public function getId(): string
    {
        return $this->id;
    }
}
