<?php

namespace App\Domain\Wod\WodScore;

interface CreateWodScoreDTOInterface
{
    public function getUserId(): int;

    public function getWodId(): int;

    public function getWodVersionId(): int;

    public function getPerformedAt(): string;

    public function getTime(): ?int;

    public function getRounds(): ?int;

    public function getRepetitions(): ?int;

    public function getWeight(): ?int;

    public function getNote(): ?string;

    public function isPrivate(): bool;
}
