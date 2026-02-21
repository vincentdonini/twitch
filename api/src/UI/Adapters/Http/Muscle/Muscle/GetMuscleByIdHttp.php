<?php

namespace App\UI\Adapters\Http\Muscle\Muscle;

use App\Domain\Muscle\Muscle\GetMuscleByIdDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class GetMuscleByIdHttp implements GetMuscleByIdDTOInterface
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
