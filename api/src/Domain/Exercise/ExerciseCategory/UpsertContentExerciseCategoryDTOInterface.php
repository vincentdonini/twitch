<?php

namespace App\Domain\Exercise\ExerciseCategory;

use Symfony\Component\Uid\Uuid;

interface UpsertContentExerciseCategoryDTOInterface
{
    public function getId(): Uuid;

    public function getLocale(): string;

    public function getTitle(): ?string;

    public function getSummary(): ?string;

    public function getDetails(): ?string;
}
