<?php

namespace App\UI\Adapters\Http\Exercise\Exercise;

use App\Domain\Exercise\Exercise\GetExerciseByIdDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class GetExerciseByIdHttp implements GetExerciseByIdDTOInterface
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
