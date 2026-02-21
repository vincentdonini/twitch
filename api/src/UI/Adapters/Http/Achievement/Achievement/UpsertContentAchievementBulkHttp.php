<?php

namespace App\UI\Adapters\Http\Achievement\Achievement;

use App\Domain\Achievement\Achievement\UpsertContentAchievementBulkDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UpsertContentAchievementBulkHttp implements UpsertContentAchievementBulkDTOInterface
{
    public function __construct(
        private Uuid  $id,
        private array $payload,
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getContents(): array
    {
        return $this->payload;
    }
}