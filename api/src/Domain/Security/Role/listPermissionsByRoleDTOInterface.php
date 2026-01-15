<?php

namespace App\Domain\Security\Role;


use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;

interface listPermissionsByRoleDTOInterface
{
    public function getId(): string;

    public function getPage(): int;
    public function getLimit(): int;

    public function getFilters(): ?FilterCollection;

    public function getSorts(): ?SortCollection;
}
