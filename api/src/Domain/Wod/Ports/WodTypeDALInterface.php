<?php

namespace App\Domain\Wod\Ports;

use App\Domain\Wod\Entity\WodType;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;

interface WodTypeDALInterface
{
    public function getById(string $id): ?WodType;

    public function listWodTypes(int $page = 1, int $limit = 15, $filters = []): LightPaginator;
}
