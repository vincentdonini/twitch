<?php

namespace App\UI\Adapters\Http\Muscle\MuscleArea;

use App\Domain\Muscle\MuscleArea\UpsertContentMuscleAreaBulkDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UpsertContentMuscleAreaBulkHttp implements UpsertContentMuscleAreaBulkDTOInterface
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