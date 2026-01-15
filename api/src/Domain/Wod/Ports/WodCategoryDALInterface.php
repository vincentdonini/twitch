<?php

namespace App\Domain\Wod\Ports;

use App\Domain\Wod\Entity\WodCategory;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;

interface WodCategoryDALInterface
{
    public function getById(string $id): ?WodCategory;

    public function listWodCategories(int $page = 1, int $limit = 15, $filters = []): LightPaginator;
}
