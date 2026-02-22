<?php

namespace App\UI\Adapters\Http\Wod\WodCategory;

use App\Domain\Wod\WodCategory\UpsertContentWodCategoryBulkDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UpsertContentWodCategoryBulkHttp implements UpsertContentWodCategoryBulkDTOInterface
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