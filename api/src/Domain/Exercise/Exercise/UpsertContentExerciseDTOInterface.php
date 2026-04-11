<?php

namespace App\Domain\Exercise\Exercise;

use Symfony\Component\Uid\Uuid;

interface UpsertContentExerciseDTOInterface
{
    public function getId(): Uuid;

    public function getLocale(): string;

    public function getTitle(): ?string;

    public function getSummary(): ?string;

    public function getDetails(): ?string;

    public function getTitlePlural(): ?string;
}
