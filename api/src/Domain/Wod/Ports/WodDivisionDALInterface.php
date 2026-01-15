<?php

namespace App\Domain\Wod\Ports;

use App\Domain\Wod\Entity\WodDivision;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;

interface WodDivisionDALInterface
{
    public function getById(string $id): ?WodDivision;

    public function getBySlug(string $slug): ?WodDivision;

    public function listWodDivisions(int $page = 1, int $limit = 15, $filters = []): LightPaginator;
}
