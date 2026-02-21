<?php

namespace App\UI\Adapters\Http\User\User;

use App\Domain\User\User\GetUserByIdDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class GetUserByIdHttp implements GetUserByIdDTOInterface
{
    public function __construct(
        private Uuid $id,
    ) {

    }

    public function getId(): Uuid
    {
        return $this->id;
    }
}
