<?php

namespace App\Domain\Organization\Subscription;

use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;
use Symfony\Component\Uid\Uuid;

interface ListSubscriptionsByPlaceIdDTOInterface
{
    public function getPlaceId(): Uuid;

    public function getFormulaId(): Uuid;

    public function getPage(): int;

    public function getLimit(): int;

    public function getFilters(): ?FilterCollection;

    public function getSorts(): ?SortCollection;
}
