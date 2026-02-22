<?php

namespace App\UI\Adapters\Http\Wod\WodCategory;

use App\Domain\Wod\WodCategory\GetWodCategoryByIdDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class GetWodCategoryByIdHttp implements GetWodCategoryByIdDTOInterface
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
