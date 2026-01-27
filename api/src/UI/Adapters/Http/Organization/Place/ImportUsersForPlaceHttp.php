<?php

namespace App\UI\Adapters\Http\Organization\Place;

use App\Domain\Organization\Place\ImportUsersForPlaceDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class ImportUsersForPlaceHttp implements ImportUsersForPlaceDTOInterface
{
    public function __construct(
        private Uuid  $id,
        private string $csvPath,
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }
    public function getCsvPath(): string
    {
        return $this->csvPath;
    }
}
