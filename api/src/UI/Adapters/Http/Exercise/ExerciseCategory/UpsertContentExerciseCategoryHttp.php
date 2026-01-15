<?php

namespace App\UI\Adapters\Http\Exercise\ExerciseCategory;

use App\Domain\Exercise\ExerciseCategory\UpsertContentExerciseCategoryDTOInterface;

final readonly class UpsertContentExerciseCategoryHttp implements UpsertContentExerciseCategoryDTOInterface
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
