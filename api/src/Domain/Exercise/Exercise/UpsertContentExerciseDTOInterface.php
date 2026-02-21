<?php

namespace App\Domain\Exercise\Exercise;

interface UpsertContentExerciseDTOInterface
{
    public function getId(): string;

    public function getLocale(): string;

    public function getTitle(): ?string;

    public function getSummary(): ?string;

    public function getDetails(): ?string;
}

