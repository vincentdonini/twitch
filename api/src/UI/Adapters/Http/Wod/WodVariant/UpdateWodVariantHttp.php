<?php

namespace App\UI\Adapters\Http\Wod\WodVariant;

use App\Domain\Wod\WodVariant\UpdateWodVariantDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UpdateWodVariantHttp implements UpdateWodVariantDTOInterface
{
    public function __construct(
        private Uuid  $variantId,
        private array $payload,
    ) {
    }

    public function getVariantId(): Uuid
    {
        return $this->variantId;
    }

    public function getDivisionId(): ?Uuid
    {
        $id = $this->payload['divisionId'] ?? null;
        return $id ? Uuid::fromString($id) : null;
    }

    public function getGender(): ?string
    {
        return $this->payload['gender'] ?? null;
    }

    public function getAgeRangeId(): ?Uuid
    {
        $id = $this->payload['ageRangeId'] ?? null;
        return $id ? Uuid::fromString($id) : null;
    }

    public function getRounds(): ?int
    {
        return isset($this->payload['rounds']) ? (int) $this->payload['rounds'] : null;
    }

    public function getTimeCap(): ?int
    {
        return isset($this->payload['timeCap']) ? (int) $this->payload['timeCap'] : null;
    }

    public function getExercises(): ?array
    {
        return array_key_exists('exercises', $this->payload) ? $this->payload['exercises'] : null;
    }
}
