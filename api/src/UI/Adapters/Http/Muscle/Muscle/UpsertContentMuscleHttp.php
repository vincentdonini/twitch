<?php

namespace App\UI\Adapters\Http\Muscle\Muscle;

use App\Domain\Muscle\Muscle\UpsertContentMuscleDTOInterface;

final readonly class UpsertContentMuscleHttp implements UpsertContentMuscleDTOInterface
{
    public function __construct(
        private string $id,
        private string $locale,
        private array  $payload,
    ) {

    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getTitle(): ?string
    {
        return $this->payload['title'] ?? null;
    }

    public function getSummary(): ?string
    {
        return $this->payload['summary'] ?? null;
    }

    public function getDetails(): ?string
    {
        return $this->payload['details'] ?? null;
    }
}
