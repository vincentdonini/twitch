<?php

namespace App\UI\Adapters\Http\Achievement\Achievement;

use App\Domain\Achievement\Achievement\GetAchievementByIdDTOInterface;

final readonly class GetAchievementByIdHttp implements GetAchievementByIdDTOInterface
{
    public function __construct(
        private string $id,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }
}
