<?php

namespace App\UI\Adapters\Http\Achievement\Achievement;

use App\Domain\Achievement\Achievement\GetAchievementByIdDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class GetAchievementByIdHttp implements GetAchievementByIdDTOInterface
{
    public function __construct(
        private Uuid $id,
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }
}
