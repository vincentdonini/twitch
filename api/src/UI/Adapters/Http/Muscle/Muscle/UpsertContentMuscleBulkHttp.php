<?php

namespace App\UI\Adapters\Http\Muscle\Muscle;

use App\Domain\Muscle\Muscle\UpsertContentMuscleBulkDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UpsertContentMuscleBulkHttp implements UpsertContentMuscleBulkDTOInterface
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
