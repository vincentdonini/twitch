<?php

namespace App\Domain\Wod\Wod;

use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;

interface GetWodLeaderboardDTOInterface
{
    public function getWodId(): string;

    public function getWodDivisionId(): ?string;

    public function getGender(): ?string;

    public function getMetric(): ?string;

    public function getPage(): int;

    public function getLimit(): int;

    public function getFilters(): ?FilterCollection;

    public function getSorts(): ?SortCollection;
}
