<?php

namespace App\UI\Adapters\Http\Security\Permission;


use App\Domain\Security\Permission\GetPermissionByIdDTOInterface;

final readonly class GetPermissionByIdHttp implements GetPermissionByIdDTOInterface
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
