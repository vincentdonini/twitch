<?php

namespace App\Domain\Wod\Wod;

use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;
use Symfony\Component\Uid\Uuid;

interface GetWodLeaderboardDTOInterface
{
    public function getWodId(): Uuid;

    public function getWodDivisionId(): ?Uuid;

    public function getGender(): ?string;

    public function getMetric(): ?string;

    public function getPage(): int;

    public function getLimit(): int;

    public function getFilters(): ?FilterCollection;

    public function getSorts(): ?SortCollection;
}
