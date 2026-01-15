<?php

namespace App\Domain\Security\Role;

use App\Domain\Security\Ports\PermissionDALInterface;
use App\Domain\Security\Ports\RoleDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use Doctrine\ORM\EntityNotFoundException;
use InvalidArgumentException;

class listPermissionsByRoleUseCase
{
    public function __construct(
        private RoleDALInterface       $roleDAL,
        private PermissionDALInterface $permissionDAL,
    ) {
    }

    public function execute(listPermissionsByRoleDTOInterface $dto): LightPaginator
    {
        if ($dto->getPage() !== null && $dto->getPage() < 1) {
            throw new InvalidArgumentException();
        }

        if ($dto->getLimit() !== null && $dto->getLimit() < 1) {
            throw new InvalidArgumentException();
        }

        $role = $this->roleDAL->getById($dto->getId());

        if (!$role) {
            throw new EntityNotFoundException();
        }

        return $this->permissionDAL->listPermissionsByRole(
            roleId : $dto->getId(),
            page   : $dto->getPage(),
            limit  : $dto->getLimit(),
            filters: $dto->getFilters(),
            sorts  : $dto->getSorts(),
        );
    }
}
