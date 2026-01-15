<?php

namespace App\Domain\Security\Permission;

use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;

interface ListPermissionDTOInterface
{
    public function getPage(): int;
    public function getLimit(): int;

    public function getFilters(): ?FilterCollection;

    public function getSorts(): ?SortCollection;
}
