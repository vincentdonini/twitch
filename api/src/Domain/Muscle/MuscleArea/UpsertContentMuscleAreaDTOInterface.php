<?php

namespace App\Domain\Muscle\MuscleArea;

use Symfony\Component\Uid\Uuid;

interface UpsertContentMuscleAreaDTOInterface
{
    public function getId(): Uuid;

    public function getLocale(): string;

    public function getTitle(): ?string;

    public function getSummary(): ?string;

    public function getDetails(): ?string;
}
