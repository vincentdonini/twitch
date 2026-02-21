<?php

namespace App\Domain\Exercise\ExerciseCategory;

interface UpsertContentExerciseCategoryDTOInterface
{
    public function getId(): string;

    public function getLocale(): string;

    public function getTitle(): ?string;

    public function getSummary(): ?string;

    public function getDetails(): ?string;
}
