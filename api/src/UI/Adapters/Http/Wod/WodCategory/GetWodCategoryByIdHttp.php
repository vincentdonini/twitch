<?php

namespace App\UI\Adapters\Http\Wod\WodCategory;

use App\Domain\Wod\WodCategory\GetWodCategoryByIdDTOInterface;

class GetWodCategoryByIdHttp implements GetWodCategoryByIdDTOInterface
{
    public function __construct(
        private readonly string $id,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }
}
