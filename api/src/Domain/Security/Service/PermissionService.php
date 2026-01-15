<?php

namespace App\Domain\Security\Service;

use App\Application\Common\CollectionMapper;
use App\Application\Security\DTO\PermissionDTO;
use App\Domain\Common\Filter\EntityFieldFilter;
use App\Domain\Security\Entity\Permission;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Filters\FilterCollection;
use Symfony\Component\HttpFoundation\RequestStack;

class PermissionService
{
    use LocaleTrait;

    private array $filterMapping;

    public function __construct(
        private readonly RequestStack      $requestStack,
        private readonly EntityFieldFilter $EntityFieldFilter,
    ) {

    }

    public function transformToDTO(Permission $permission, FilterCollection $filters = null): ?PermissionDTO
    {
        return new PermissionDTO(
            id      : $permission->getId(),
            code    : $permission->getCode(),
            label   : $permission->getLabel(),
            resource: $permission->getResource(),
        );
    }

    public function transformCollectionToDTO(array $permissions, FilterCollection $filters = null): array
    {
        return CollectionMapper::mapAndFilter(
            $permissions,
            fn(Permission $permission) => $this->transformToDTO($permission, $filters)
        );
    }
}
