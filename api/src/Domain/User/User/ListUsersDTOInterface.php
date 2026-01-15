<?php

namespace App\Domain\User\User;

interface ListUsersDTOInterface
{
    public function getPage(): int;
    public function getLimit(): int;
    public function getFilters(): ?array;
}

