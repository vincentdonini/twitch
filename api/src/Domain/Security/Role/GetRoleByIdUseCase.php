<?php

namespace App\Domain\Security\Role;

use App\Domain\Security\Entity\Role;
use App\Domain\Security\Ports\RoleDALInterface;
use Doctrine\ORM\EntityNotFoundException;

class GetRoleByIdUseCase
{
    public function __construct(
        private readonly RoleDALInterface $roleDAL,
    ) {

    }

    public function execute(GetRoleByIdDTOInterface $dto): Role
    {
        $role = $this->roleDAL->getById($dto->getId());
        if (!$role instanceof Role) {
            throw new EntityNotFoundException();
        }

        return $role;
    }
}

