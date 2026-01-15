<?php

namespace App\Domain\User\User;

use App\Domain\User\Ports\UserDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use InvalidArgumentException;

class ListUsersUseCase
{
    public function __construct(
        private readonly UserDALInterface $exerciseDAL,
    )
    {

    }

    public function execute(ListUsersDTOInterface $dto): LightPaginator
    {
        if($dto->getPage() && $dto->getPage() < 0) {
            throw new InvalidArgumentException();
        }

        return $this->exerciseDAL->listUsers(
           page: $dto->getPage(),
           limit: $dto->getLimit(),
           filters: $dto->getFilters(),
        );
    }
}

