<?php

namespace App\Domain\Wod\WodVariant;

use Symfony\Component\Uid\Uuid;

interface UpdateWodVariantDTOInterface
{
    public function getVariantId(): Uuid;

    public function getDivisionId(): ?Uuid;

    public function getGender(): ?string;

    public function getAgeRangeId(): ?Uuid;

    public function getRounds(): ?int;

    public function getTimeCap(): ?int;

    public function getExercises(): ?array;
}
