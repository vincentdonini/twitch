<?php

namespace App\Domain\Security\Role;

use App\Domain\Security\Ports\RoleDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use InvalidArgumentException;

readonly class ListRolesUseCase
{
    public function __construct(
        private RoleDALInterface $roleDAL,
    ) {
    }

    public function execute(ListRoleDTOInterface $dto): LightPaginator
    {
        if ($dto->getPage() && $dto->getPage() < 0) {
            throw new InvalidArgumentException();
        }

        return $this->roleDAL->listRoles(
            page   : $dto->getPage(),
            limit  : $dto->getLimit(),
            filters: $dto->getFilters(),
            sorts  : $dto->getSorts(),
        );
    }
}
