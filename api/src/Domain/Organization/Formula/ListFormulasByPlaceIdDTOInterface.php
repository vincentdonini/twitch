<?php

namespace App\Domain\Organization\Formula;

use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;
use Symfony\Component\Uid\Uuid;

interface ListFormulasByPlaceIdDTOInterface
{
    public function getPlaceId(): Uuid;

    public function getPage(): int;

    public function getLimit(): int;

    public function getFilters(): ?FilterCollection;

    public function getSorts(): ?SortCollection;
}
