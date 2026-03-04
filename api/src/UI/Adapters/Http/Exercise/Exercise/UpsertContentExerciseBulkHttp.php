<?php

namespace App\UI\Adapters\Http\Exercise\Exercise;

use App\Domain\Exercise\Exercise\UpsertContentExerciseBulkDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UpsertContentExerciseBulkHttp implements UpsertContentExerciseBulkDTOInterface
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
