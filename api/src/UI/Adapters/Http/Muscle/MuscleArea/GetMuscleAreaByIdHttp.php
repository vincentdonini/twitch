<?php

namespace App\UI\Adapters\Http\Muscle\MuscleArea;

use App\Domain\Muscle\MuscleArea\GetMuscleAreaByIdDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class GetMuscleAreaByIdHttp implements GetMuscleAreaByIdDTOInterface
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
