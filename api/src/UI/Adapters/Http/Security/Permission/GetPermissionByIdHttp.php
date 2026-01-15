<?php

namespace App\UI\Adapters\Http\Security\Permission;


use App\Domain\Security\Permission\GetPermissionByIdDTOInterface;

class GetPermissionByIdHttp implements GetPermissionByIdDTOInterface
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
