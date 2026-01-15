<?php

namespace App\Domain\Muscle\MuscleGroup;

interface UpsertContentMuscleGroupDTOInterface
{
    public function getId(): string;
    public function getLocale(): string;
    public function getTitle(): ?string;
    public function getSummary(): ?string;
    public function getDetails(): ?string;
}

