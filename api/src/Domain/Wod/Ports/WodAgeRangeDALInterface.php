<?php

namespace App\Domain\Wod\Ports;

use App\Domain\Wod\Entity\WodAgeRange;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;

interface WodAgeRangeDALInterface
{
    public function getById(string $id): ?WodAgeRange;

    public function getBySlug(string $slug): ?WodAgeRange;

    public function listWodAgeRanges(int $page = 1, int $limit = 15, $filters = []): LightPaginator;
}
