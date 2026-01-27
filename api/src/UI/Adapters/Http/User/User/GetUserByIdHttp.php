<?php

namespace App\UI\Adapters\Http\User\User;

use App\Domain\User\User\GetUserByIdDTOInterface;
use Symfony\Component\Uid\Uuid;

class GetUserByIdHttp implements GetUserByIdDTOInterface
{
    public function __construct(
        private readonly Uuid $id,
    ) {

    }

    public function getId(): Uuid
    {
        return $this->id;
    }
}
