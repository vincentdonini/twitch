<?php

namespace App\UI\Adapters\Http\Achievement\Achievement;

use App\Domain\Achievement\Achievement\UpsertContentAchievementDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UpsertContentAchievementHttp implements UpsertContentAchievementDTOInterface
{
    public function __construct(
        private Uuid   $id,
        private string $locale,
        private array  $payload,
    ) {

    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getTitle(): ?string
    {
        return $this->payload['title'] ?? null;
    }

    public function getDescription(): ?string
    {
        return $this->payload['description'] ?? null;
    }
}
