<?php

namespace App\Domain\Wod\Repository;

use App\Domain\Wod\Entity\WodVersionVariantMovement;

interface WodVersionVariantMovementRepositoryInterface
{
    public function findOneById(string $id): ?WodVersionVariantMovement;

    public function findOneBySlug(string $slug): ?WodVersionVariantMovement;

    /** @return WodVersionVariantMovement[] */
    public function findAll(): array;

    public function save(WodVersionVariantMovement $wodVersionVariantMovement): void;

    public function delete(WodVersionVariantMovement $wodVersionVariantMovement): void;
}
