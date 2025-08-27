<?php

namespace App\Domain\Wod\Repository;

use App\Domain\Wod\Entity\WodVersionVariant;

interface WodVersionVariantRepositoryInterface
{
    public function findOneById(string $id): ?WodVersionVariant;

    public function findOneByName(string $name): ?WodVersionVariant;

    /** @return WodVersionVariant[] */
    public function findAll(): array;

    public function save(WodVersionVariant $wodVersionVariant): void;

    public function delete(WodVersionVariant $wodVersionVariant): void;
}
