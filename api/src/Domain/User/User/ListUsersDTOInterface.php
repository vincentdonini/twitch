<?php

namespace App\Domain\User\User;

use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;

interface ListUsersDTOInterface
{
    public function getPage(): int;

    public function getLimit(): int;

    public function getFilters(): ?FilterCollection;

    public function getSorts(): ?SortCollection;

    public function getSearch(): string;
}
