<?php

namespace App\Domain\User\Ports;

use App\Domain\User\Entity\User;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;

interface UserDALInterface
{
    public function getById(string $id): ?User;

    public function listUsers(int $page = 1, int $limit = 15, $filters = []): LightPaginator;
}
