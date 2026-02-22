<?php

namespace App\UI\Adapters\Http\Muscle\MuscleGroup;

use App\Domain\Muscle\MuscleGroup\UpsertContentMuscleGroupBulkDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UpsertContentMuscleGroupBulkHttp implements UpsertContentMuscleGroupBulkDTOInterface
{
    public function __construct(
        private Uuid  $id,
        private array $payload,
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getContents(): array
    {
        return $this->payload;
    }
}