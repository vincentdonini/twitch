<?php

namespace App\UI\Adapters\Http\Muscle\MuscleGroup;

use App\Domain\Muscle\MuscleGroup\GetMuscleGroupByIdDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class GetMuscleGroupByIdHttp implements GetMuscleGroupByIdDTOInterface
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
