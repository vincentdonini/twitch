<?php

namespace App\Domain\Security\Permission;

use App\Domain\Security\Entity\Permission;
use App\Domain\Security\Ports\PermissionDALInterface;
use Doctrine\ORM\EntityNotFoundException;

class GetPermissionByIdUseCase
{
    public function __construct(
        private readonly PermissionDALInterface $exerciseDAL,
    )
    {

    }

    public function execute(GetPermissionByIdDTOInterface $dto): Permission
    {
        $exercise = $this->exerciseDAL->getById($dto->getId());
        if(!$exercise instanceof Permission){
            throw new EntityNotFoundException();
        }

        return $exercise;
    }
}

