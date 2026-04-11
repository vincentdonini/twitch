<?php

namespace App\UI\Adapters\Http\Muscle\MuscleSegment;

use Symfony\Component\Uid\Uuid;

final readonly class UpsertContentMuscleSegmentBulkHttp
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
