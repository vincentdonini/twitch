<?php

namespace App\Domain\Security\Service;

use App\Application\Common\CollectionMapper;
use App\Application\Security\DTO\RoleDTO;
use App\Domain\Common\Filter\EntityFieldFilter;
use App\Domain\Security\Entity\Role;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Filters\FilterCollection;
use Symfony\Component\HttpFoundation\RequestStack;

class RoleService
{
    use LocaleTrait;

    private array $filterMapping;

    public function __construct(
        private readonly RequestStack      $requestStack,
        private readonly EntityFieldFilter $EntityFieldFilter,
    ) {

    }

    public function transformToDTO(Role $role, FilterCollection $filters = null): ?RoleDTO
    {
        return new RoleDTO(
            id   : $role->getId(),
            code : $role->getCode(),
            label: $role->getLabel(),
        );
    }

    public function transformCollectionToDTO(array $roles, FilterCollection $filters = null): array
    {
        return CollectionMapper::mapAndFilter(
            $roles,
            fn(Role $role) => $this->transformToDTO($role, $filters)
        );
    }
}
