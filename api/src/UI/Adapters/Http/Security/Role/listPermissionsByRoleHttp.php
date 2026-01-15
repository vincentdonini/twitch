<?php

namespace App\UI\Adapters\Http\Security\Role;

use App\Domain\Security\Role\listPermissionsByRoleDTOInterface;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;

readonly class listPermissionsByRoleHttp implements listPermissionsByRoleDTOInterface
{
    public function __construct(
        private string            $id,
        private ?int              $page = null,
        private ?int              $limit = null,
        private ?FilterCollection $filters = null,
        private ?SortCollection   $sorts = null,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPage(): int
    {
        return $this->page ?? 1;
    }

    public function getLimit(): int
    {
        return $this->limit ?? 15;
    }

    public function getFilters(): ?FilterCollection
    {
        return $this->filters;
    }

    public function getSorts(): ?SortCollection
    {
        return $this->sorts;
    }
}
