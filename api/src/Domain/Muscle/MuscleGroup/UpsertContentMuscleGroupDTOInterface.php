<?php

namespace App\Domain\Muscle\MuscleGroup;

use Symfony\Component\Uid\Uuid;

interface UpsertContentMuscleGroupDTOInterface
{
    public function getId(): Uuid;

    public function getLocale(): string;

    public function getTitle(): ?string;

    public function getSummary(): ?string;

    public function getDetails(): ?string;
}
