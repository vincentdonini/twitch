<?php

namespace App\Domain\Security\Permission;

use App\Domain\Security\Ports\PermissionDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use InvalidArgumentException;

readonly class ListPermissionUseCase
{
    public function __construct(
        private PermissionDALInterface $permissionDAL,
    ) {
    }

    public function execute(ListPermissionDTOInterface $dto): LightPaginator
    {
        if ($dto->getPage() && $dto->getPage() < 0) {
            throw new InvalidArgumentException();
        }

        return $this->permissionDAL->listPermission(
            page   : $dto->getPage(),
            limit  : $dto->getLimit(),
            filters: $dto->getFilters(),
            sorts  : $dto->getSorts(),
        );
    }
}
