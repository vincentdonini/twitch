<?php

namespace App\Domain\User\User;


use Symfony\Component\Uid\Uuid;

interface GetUserByIdDTOInterface
{
    public function getId(): Uuid;
}
