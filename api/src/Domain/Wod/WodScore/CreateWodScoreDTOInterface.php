<?php

namespace App\Domain\Wod\WodScore;

use Symfony\Component\Uid\Uuid;

interface CreateWodScoreDTOInterface
{
    public function getUserId(): Uuid;

    public function getWodId(): Uuid;

    public function getWodVersionId(): Uuid;

    public function getPerformedAt(): string;

    public function getTime(): ?int;

    public function getRounds(): ?int;

    public function getRepetitions(): ?int;

    public function getWeight(): ?int;

    public function getNote(): ?string;

    public function isPrivate(): bool;
}
